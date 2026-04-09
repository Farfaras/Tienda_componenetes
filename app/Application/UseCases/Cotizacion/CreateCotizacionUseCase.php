<?php

namespace App\Application\UseCases\Cotizacion;

use App\Application\DTOs\Cotizacion\CreateCotizacionDTO;
use App\Domain\Repositories\CotizacionRepositoryInterface;

class CreateCotizacionUseCase
{
    public function __construct(
        private CotizacionRepositoryInterface $cotizacionRepository
    ) {}

    public function execute(CreateCotizacionDTO $dto)
    {
        return $this->cotizacionRepository->create($dto->toArray());
    }
}