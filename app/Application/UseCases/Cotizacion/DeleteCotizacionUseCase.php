<?php

namespace App\Application\UseCases\Cotizacion;

use App\Domain\Repositories\CotizacionRepositoryInterface;

class DeleteCotizacionUseCase
{
    public function __construct(
        private CotizacionRepositoryInterface $cotizacionRepository
    ) {}

    public function execute(int $id): bool
    {
        return $this->cotizacionRepository->delete($id);
    }
}