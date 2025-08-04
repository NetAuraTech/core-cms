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

        $hasPermissions = method_exists($user, 'can');

        return collect($this->menuItems)->filter(function ($item) use ($user, $hasPermissions) {
            if (isset($item['children']) && is_array($item['children'])) {
                $item['children'] = collect($item['children'])->filter(function ($child) use ($user, $hasPermissions) {
                    if (!isset($child['can']) || !$hasPermissions) {
                        return true;
                    }
                    return $user->can($child['can']);
                })->toArray();

                return !empty($item['children']);
            }

            if (!isset($item['can']) || !$hasPermissions) {
                return true;
            }

            return $user->can($item['can']);
        })->toArray();
    }
}