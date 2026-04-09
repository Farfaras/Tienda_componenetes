<?php

namespace App\Application\UseCases\Cotizacion;

use App\Domain\Repositories\CotizacionRepositoryInterface;

class GetCotizacionByIdUseCase
{
    public function __construct(
        private CotizacionRepositoryInterface $cotizacionRepository
    ) {}

    public function execute(int $id)
    {
        return $this->cotizacionRepository->findById($id);
    }
}