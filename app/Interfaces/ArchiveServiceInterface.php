<?php

namespace App\Interfaces;

use App\Models\ArchivedDette;

interface ArchiveServiceInterface
{
    public function archivePaidDebts();
    public function getArchivedDebts($filter = []);
    public function getArchivedDebtsByClient($filter);
    public function restoreDebtsByDate($date);
    public function restoreDebtById($id);
    public function restoreDebtsByClientId($client_id);
    public function createDetteFromData(ArchivedDette $archivedDette);

    }
