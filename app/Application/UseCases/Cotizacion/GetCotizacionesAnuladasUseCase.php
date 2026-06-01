<?php

namespace App\Application\UseCases\Cotizacion;

use App\Domain\Repositories\CotizacionRepositoryInterface;

class GetCotizacionesAnuladasUseCase
{
    public function __construct(
        private CotizacionRepositoryInterface $cotizacionRepository
    ) {}

    public function execute()
    {
        return $this->cotizacionRepository->getAnuladas();
    }
}