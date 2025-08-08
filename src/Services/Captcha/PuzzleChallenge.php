<?php

namespace Netauratech\CoreCms\Services\Captcha;

use Illuminate\Support\Facades\Session;
use Netauratech\CoreCms\Contracts\ChallengeInterface;
use Illuminate\Http\Request;

class PuzzleChallenge implements ChallengeInterface
{
    public const WIDTH = 350;
    public const HEIGHT = 200;
    public const PIECE_WIDTH = 80;
    public const PIECE_HEIGHT = 50;

    private const SESSION_KEY = "puzzles";

    private const PRECISION = 2;

    public function __construct(private readonly Request $request)
    {
    }

    /**
     * Generates a unique key for a new puzzle challenge and stores its solution in the session.
     *
     * @return string The key (timestamp) of the generated puzzle.
     */
    public function generateKey(): string
    {
        $now = time();

        $x = mt_rand(0, self::WIDTH - self::PIECE_WIDTH);
        $y = mt_rand(0, self::HEIGHT - self::PIECE_HEIGHT);

        $puzzles = Session::get(self::SESSION_KEY, []);
        $puzzles[] = ['key' => $now, 'solution' => [$x, $y]];
        Session::put(self::SESSION_KEY, array_slice($puzzles, -10));

        return (string) $now;
    }

    /**
     * Retrieves the solution to a puzzle by its key.
     *
     * @param string $key The key to the puzzle.
     * @return array|null The [x, y] array of the solution or null if not found.
     */
    public function getSolution(string $key): array|null
    {
        $puzzles = Session::get(self::SESSION_KEY, []);
        foreach ($puzzles as $puzzle) {
            if ($puzzle['key'] !== intval($key)) {
                continue;
            }

            return $puzzle['solution'];
        }
        return null;
    }

    /**
     * Checks the answer without deleting the puzzle from the session.
     *
     * @param string $key The puzzle key.
     * @param string $answer The answer (e.g., “x-y”).
     * @return bool True if the answer is correct.
     */
    public function check(string $key, string $answer): bool
    {
        $expected = $this->getSolution($key);

        if (!$expected) {
            return false;
        }

        $got = $this->stringToPosition($answer);

        return abs($expected[0] - $got[0]) <= self::PRECISION && abs($expected[1] - $got[1]) <= self::PRECISION;
    }

    /**
     * Checks the answer and removes the puzzle from the session.
     *
     * @param string $key The puzzle key.
     * @param string $answer The answer (e.g., “x-y”).
     * @return bool True if the answer is correct.
     */
    public function verify(string $key, string $answer): bool
    {
        $expected = $this->getSolution($key);

        if (!$expected) {
            return false;
        }

        $puzzles = Session::get(self::SESSION_KEY, []);
        Session::put(self::SESSION_KEY, array_filter($puzzles, fn (array $puzzle) => $puzzle['key'] !== intval($key)));

        $got = $this->stringToPosition($answer);
        return abs($expected[0] - $got[0]) <= self::PRECISION && abs($expected[1] - $got[1]) <= self::PRECISION;
    }

    /**
     * Converts a string “x-y” into an array of integers [x, y].
     *
     * @param string $s The string to convert.
     * @return int[] The array [x, y] or [-1, -1] in case of a format error.
     */
    public function stringToPosition(string $s): array
    {
        $parts = explode('-', $s, 2);
        if (count($parts) !== 2) {
            return [-1, -1];
        }
        return [intval($parts[0]), intval($parts[1])];
    }
}