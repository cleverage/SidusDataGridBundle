<?php

namespace Sidus\DataGridBundle\Configuration;

use Sidus\DataGridBundle\Model\DataGrid;
use UnexpectedValueException;

/**
 * Handles datagrids configurations
 */
class DataGridConfigurationHandler
{
    /** @var DataGrid[] */
    protected $dataGrids;

    /**
     * @param DataGrid $dataGrid
     */
    public function addDataGrid(DataGrid $dataGrid)
    {
        $this->dataGrids[$dataGrid->getCode()] = $dataGrid;
    }

    /**
     * @return DataGrid[]
     */
    public function getDataGrids()
    {
        return $this->dataGrids;
    }

    /**
     * @param string $code
     *
     * @return DataGrid
     * @throws UnexpectedValueException
     */
    public function getDataGrid($code)
    {
        if (empty($this->dataGrids[$code])) {
            throw new UnexpectedValueException("No data-grid with code : {$code}");
        }

        return $this->dataGrids[$code];
    }

    /**
     * @param string $code
     *
     * @return bool
     * @throws UnexpectedValueException
     */
    public function hasDataGrid($code)
    {
        return isset($this->dataGrids[$code]);
    }
}
