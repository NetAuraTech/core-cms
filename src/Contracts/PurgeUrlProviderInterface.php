<?php

namespace Netauratech\CoreCms\Contracts;

use Illuminate\Database\Eloquent\Model;

interface PurgeUrlProviderInterface
{
    /**
     * Récupère une liste d'URLs à purger basées sur une instance de contenu spécifique.
     * Cette méthode est appelée lorsque seul un contenu particulier est modifié.
     *
     * @param Model $content L'instance de contenu qui a été modifiée.
     * @return array<string> Une liste d'URLs relatives à purger.
     */
    public function getUrlsToPurge(Model $content): array;

    /**
     * Récupère une liste de toutes les URLs gérées par ce fournisseur.
     * Cette méthode est principalement utilisée pour une purge globale du cache
     * (par exemple, lors de la mise à jour des éléments globaux comme le header/footer).
     *
     * @return array<string> Une liste de toutes les URLs relatives gérées par ce fournisseur.
     */
    public function getAllManagedUrls(): array;
}