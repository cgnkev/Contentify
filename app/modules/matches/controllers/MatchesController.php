<?php namespace App\Modules\Matches\Controllers;

use App\Modules\Matches\Models\Match;
use URL, HTML, FrontController;

class MatchesController extends FrontController {

    public function __construct()
    {
        $this->modelName = 'Match';

        parent::__construct();
    }

    public function index()
    {
        $this->pageView('matches::filter');

        $this->indexPage([
            'buttons'       => null,
            'brightenFirst' => false,
            'filter'        => true,
            'searchFor' => ['rightTeam', 'title'], 
            'tableHead'     => [
                trans('app.date')               => 'played_at',
                trans('Game')                   => 'game_id',
                trans('matches::right_team')    => 'right_team_id',
                trans('matches::score')         => 'left_score'
            ],
            'tableRow'      => function($match)
            {
                if ($match->game->icon) {
                    $game = HTML::image(
                        $match->game->uploadPath().$match->game->icon, 
                        $match->game->title, 
                        ['width' => 16, 'height' => 16]
                    );
                } else {
                    $game = '';
                }

                return [
                    $match->played_at,
                    $game,
                    HTML::link(url('matches/'.$match->id), $match->right_team->title),       
                    $match->scoreCode()
                ];
            },
            'actions'       => null,
        ], 'front');
    }

    /**
     * Show a match
     * 
     * @param  int $id The id of the match
     * @return void
     */
    public function show($id)
    {
        $match = Match::findOrFail($id);

        $match->access_counter++;
        $match->save();

        $this->title($match->leftTeam->title.' '.trans('match::vs').' '.$match->rightTeam->title);

        $this->pageView('matches::show', compact('match'));
    }
    
}