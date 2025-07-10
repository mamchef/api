<?php

namespace App\Services\Interfaces;

use App\DTOs\Chef\Ticket\ChefStoreTicketDTO;
use App\DTOs\Chef\Ticket\ChefStoreTicketItemDTO;
use App\DTOs\User\Ticket\UserStoreTicketDTO;
use App\DTOs\User\Ticket\UserStoreTicketItemDTO;
use App\Models\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TicketServiceInterface
{

    /**
     * @param int $chefId
     * @param array $filters
     * @param array $relations
     * @param int|null $pagination
     * @return Collection|LengthAwarePaginator|array
     */
    public function getChefTickets(
        int $chefId,
        array $filters = [],
        array $relations = [],
        ?int $pagination = null
    ): Collection|LengthAwarePaginator|array;


    /**
     * @param int $userId
     * @param array $filters
     * @param array $relations
     * @param int|null $pagination
     * @return Collection|LengthAwarePaginator|array
     */
    public function getUserTickets(
        int $userId,
        array $filters = [],
        array $relations = [],
        ?int $pagination = null
    ): Collection|LengthAwarePaginator|array;


    /**
     * @param int $chefId
     * @param int $ticketId
     * @param array $relations
     * @return Ticket
     */
    public function getChefTicket(int $chefId, int $ticketId, array $relations = []):Ticket;


    /**
     * @param int $userId
     * @param int $ticketId
     * @param array $relations
     * @return Ticket
     */
    public function getUserTicket(int $userId, int $ticketId, array $relations = []):Ticket;

    /**
     * @param ChefStoreTicketDTO $DTO
     * @return Ticket
     */
    public function storeTicketByChef(ChefStoreTicketDTO $DTO): Ticket;

    /**
     * @param UserStoreTicketDTO $DTO
     * @return Ticket
     */
    public function storeTicketByUser(UserStoreTicketDTO $DTO): Ticket;
    /**
     * @param ChefStoreTicketItemDTO $DTO
     * @return Ticket
     */
    public function storeTicketItemByChef(ChefStoreTicketItemDTO $DTO): Ticket;

    /**
     * @param UserStoreTicketItemDTO $DTO
     * @return Ticket
     */
    public function storeTicketItemByUser(UserStoreTicketItemDTO $DTO): Ticket;
}