<?php
require_once __DIR__ . '/bootstrap.php';

/** Render only when the state changes, never on an ordinary GET/refresh. */
function render_turn(): void {
    ob_start();
    $game = $_SESSION['partida'][0];
    $game->controladorTurnos($game->getTurno());
    $scene = ob_get_clean();
    if ($_SESSION['xogador'][0]->getVida() <= 0) {
        $game->setTurno('derrota');
        ob_start();
        $game->controladorTurnos('derrota');
        $scene .= ob_get_clean();
    }
    $_SESSION['scene'] = $scene;
}
function start_game(string $name, string $class): bool {
    $classes = [
        'guerrero' => ['Guerrero', 50, 50, 30, 5],
        'paladin' => ['Paladín', 100, 30, 50, 2],
        'picaro' => ['Pícaro', 40, 80, 15, 25],
    ];
    if (!isset($classes[$class]) || trim($name) === '' || strlen($name) > 80) return false;
    [$label, $life, $attack, $defence, $dodge] = $classes[$class];
    unset($_SESSION['goblin']);
    $_SESSION['xogador'] = [new Xogador(trim($name), $label, $life, $attack, $defence, $dodge, [])];
    $_SESSION['partida'] = [new Partida(0, '')];
    $_SESSION['run_id'] = bin2hex(random_bytes(16));
    render_turn();
    return true;
}
function advance_game(): void {
    if (!isset($_SESSION['partida'], $_SESSION['xogador'])) return;
    $game = $_SESSION['partida'][0];
    $player = $_SESSION['xogador'][0];
    if ($player->getVida() <= 0) {
        $game->reset();
        unset($_SESSION['scene'], $_SESSION['run_id']);
        return;
    }
    if ($game->getTurno() === 14) {
        Dao::saveWin($_SESSION['run_id'], $_SESSION['usuario'], $player);
        $game->reset();
        unset($_SESSION['scene'], $_SESSION['run_id']);
        return;
    }
    if (isset($_SESSION['goblin'])) {
        $enemy = $_SESSION['goblin'][0];
        $game->golpe($player->getAtaque(), $enemy->getVidaGoblin());
        if ($enemy->getVidaGoblin() <= 0) {
            $game->victoria();
        } else {
            ob_start();
            $game->esquiva($enemy->getAtaqueGoblin(), $player->getEsquiva(), $player->getVida(), $player->getDefensa());
            $_SESSION['notice'] = ob_get_clean();
        }
    } else {
        $game->avanzarTurno();
    }
    render_turn();
}
