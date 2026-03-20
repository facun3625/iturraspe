<?php
// Ruta: control_stock/backend/controllers/operatingCostController.php

require_once __DIR__ . '/../models/operatingCostModel.php';

class OperatingCostController {
    private $model;

    public function __construct() {
        $this->model = new OperatingCostModel();
    }

    public function addCost($category, $description, $amount, $date) {
        return $this->model->addCost($category, $description, $amount, $date);
    }

    public function getMonthlyReport($year, $month) {
        $costs = $this->model->getCostsByMonth($year, $month);
        $total = $this->model->getTotalByMonth($year, $month);
        
        return [
            'costs' => $costs,
            'total' => $total,
            'year' => $year,
            'month' => $month
        ];
    }

    public function deleteCost($id) {
        return $this->model->deleteCost($id);
    }
}
