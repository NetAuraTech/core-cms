<?php

namespace NetAuraTech\CoreCms\Services\Admin;

class DashboardManager
{
    protected array $widgets = [];

    /**
     * Registers a widget class.
     * Other packages call this method to add their widgets.
     *
     * @param string $widgetClass
     * @return void
     */
    public function addWidget(string $widgetClass): void
    {
        $this->widgets[] = $widgetClass;
    }

    /**
     * Retrieves all registered widget classes.
     * The controller uses this method to obtain the list.
     *
     * @return array
     */
    public function getWidgets(): array
    {
        return $this->widgets;
    }
}