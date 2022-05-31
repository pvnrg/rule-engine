<?php

namespace App\Controller;

use App\Entity\User;
use App\Message\FetchTriggers;
use App\Message\SaveTriggers;
use App\Repository\NotificationRepository;
use App\Repository\RulesRepository;
use App\Repository\TriggereRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TriggerController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $queryBus,
        private readonly MessageBusInterface $commandBus,
        private readonly Security $security
    ) {
    }

    #[Route('/triggers', name: "user_triggers")]
    public function index(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            // save trigger
            $input = json_decode($request->getContent(), true);

            try {
                if (JSON_ERROR_NONE !== json_last_error()) {
                    throw new \InvalidArgumentException('Invalid JSON input.');
                }
                $this->commandBus->dispatch(new SaveTriggers(
                    $this->security->getUser()->getId(),
                    $input
                ));
            } catch (\InvalidArgumentException|\Exception $e) {
                return $this->json([
                    'error' => ($e instanceof \InvalidArgumentException) ? 'Invalid JSON input.': 'Failed to save triggers. Please try again.'
                ]);
            }
        }

        return $this->json($this->fetchData());
    }

    private function fetchData()
    {
        $envelope = $this->queryBus->dispatch(new FetchTriggers($this->security->getUser()->getId()));
        /** @var HandledStamp $stamp */
        $stamp = $envelope->last(HandledStamp::class);

        return $stamp->getResult();
    }
}
