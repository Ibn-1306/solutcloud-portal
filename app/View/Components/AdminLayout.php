<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdminLayout extends Component
{
    public function __construct(
        public string $title = 'SOLUTCLOUD — Administration',
        public string $pageTitle = 'Tableau de bord',
        public string $description = 'Pilotez les opérations SOLUTCLOUD depuis un espace centralisé.',
    ) {}

    public function render(): View
    {
        return view('components.admin-layout');
    }
}
