<?php

namespace App\Application\UseCases\Cotizacion;

use App\Domain\Repositories\CotizacionRepositoryInterface;

class CountCotizacionesActivasUseCase
{
    public function __construct(
        private CotizacionRepositoryInterface $cotizacionRepository
    ) {}

    public function execute(): int
    {
        return $this->cotizacionRepository->countActivas();
    }
}