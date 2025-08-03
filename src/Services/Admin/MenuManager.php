<?php

namespace NetAuraTech\CoreCms\Services\Admin;

class MenuManager
{
    protected array $menuItems = [];

    /**
     * Registers a menu element.
     * Other packages call this method to add their widgets.
     *
     * @param string $id The controller uses this method to obtain the list.
     * @param array $item The menu item to be recorded.
     * @return void
     */
    public function registerMenuItem(string $id, array $item): void
    {
        $this->menuItems[$id] = $item;
    }

    /**
     * Retrieves all registered menu items.
     * The controller uses this method to obtain the list.
     *
     * @return array
     */
    public function getMenuItems(): array
    {
        return $this->menuItems;
    }
}