<?php

namespace Netauratech\CoreCms\Services\Captcha;

use Illuminate\Http\Response;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Netauratech\CoreCms\Contracts\ChallengeGeneratorInterface;
use Random\RandomException;

class PuzzleGenerator implements ChallengeGeneratorInterface
{

    public function __construct(private readonly PuzzleChallenge $challenge)
    {
    }

    /**
     * Generates the captcha puzzle image.
     *
     * @param string $key The challenge key for which to generate the image.
     * @return Response The captcha image in PNG format.
     * @throws RandomException
     */
    public function generate(string $key): Response
    {
        $position = $this->challenge->getSolution($key);

        if (!$position) {
            return new Response('Captcha solution not found.', 404);
        }

        [$x, $y] = $position;

        $basePath = realpath(__DIR__ . '/../../resources/assets/captcha');
        if (!$basePath) {
            return new Response('Captcha image resources not found.', 500);
        }

        $backgroundPath = sprintf("%s/captcha%d.png", $basePath, random_int(1, 5));
        $holePath = sprintf('%s/hole.png', $basePath);

        try {
            $image = Image::make($backgroundPath);

            $holeMask = Image::make($holePath);
            $holeMask->resize(PuzzleChallenge::PIECE_WIDTH, PuzzleChallenge::PIECE_HEIGHT);

            $puzzlePieceContent = clone $image;
            $puzzlePieceContent->crop(PuzzleChallenge::PIECE_WIDTH, PuzzleChallenge::PIECE_HEIGHT, $x, $y);
            $puzzlePieceContent->mask($holeMask, true);

            $image->resizeCanvas(
                $image->width() + PuzzleChallenge::PIECE_WIDTH,
                $image->height(),
                'left',
                false,
                array(0, 0, 0, 0)
            );
            $image->insert($puzzlePieceContent, 'top-left', $image->width() - PuzzleChallenge::PIECE_WIDTH, 0);

            $visualHole = Image::make($holePath);
            $visualHole->resize(PuzzleChallenge::PIECE_WIDTH, PuzzleChallenge::PIECE_HEIGHT);
            $image->insert($visualHole, 'top-left', $x, $y);

            if (ob_get_level() > 0) {
                ob_clean();
            }

            $finalEncodedImage = $image->encode('png');
            $response = new Response($finalEncodedImage, 200);
            $response->header('Content-Type', 'image/png');
            return $response;

        } catch (\Exception $e) {
            return new Response('Captcha generation failed.', 500);
        }
    }
}