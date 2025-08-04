<?php

namespace Netauratech\CoreCms\Services\Admin;

use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        return collect($this->menuItems)->filter(function ($item) use ($user) {
            if (!isset($item['can'])) {
                return true;
            }

            if (!method_exists($user, 'can')) {
                return true;
            }

            return $user->can($item['can']);
        })->toArray();
    }
}