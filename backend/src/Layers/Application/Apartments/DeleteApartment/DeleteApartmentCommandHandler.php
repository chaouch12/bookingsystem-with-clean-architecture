<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\DeleteApartment;

use App\Layers\Application\Shared\Messaging\CommandHandler;
use App\Layers\Application\Shared\Validation\MessageValidator;
use App\Layers\Domain\Appartment\AppartmentErrors;
use App\Layers\Domain\Appartment\Repo\AppartmentRepository;
use App\Layers\Domain\Shared\Result;

/**
 * @implements CommandHandler<DeleteApartmentCommand, Result>
 */
final readonly class DeleteApartmentCommandHandler implements CommandHandler
{
    public function __construct(
        private AppartmentRepository $appartmentRepository,
        private MessageValidator $messageValidator,
    ) {
    }

    public function handle(object $command): Result
    {
        $this->messageValidator->validate($command);

        $appartment = $this->appartmentRepository->find($command->id);

        if ($appartment === null) {
            return Result::failure(AppartmentErrors::notFound());
        }

        $this->appartmentRepository->remove($appartment, true);

        return Result::success();
    }
}
