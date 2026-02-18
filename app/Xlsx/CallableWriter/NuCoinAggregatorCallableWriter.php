<?php

declare(strict_types=1);

namespace Lazis\Api\Xlsx\CallableWriter;

use Lazis\Api\Xlsx\CallableWriterInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Http\Message\ResponseInterface;
use Schnell\Http\Code as HttpCode;
use Slim\Psr7\Response;
use Slim\Psr7\Stream;

use function array_slice;
use function array_push;
use function array_map;
use function sprintf;
use function fopen;
use function rewind;

/**
 * @author Paulus Gandung Prakosa <gandung@infradead.org>
 */
class NuCoinAggregatorCallableWriter implements CallableWriterInterface
{
    /**
     * @param string $file
     * @param array $entities
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function concreteCallable(string $file, array $entities): ResponseInterface
    {
        $spreadsheet = IOFactory::load($file);
        $activeSheet = $spreadsheet->getActiveSheet();
        $cellCollection = $activeSheet->getCellCollection();

        $hcolnum = Coordinate::columnIndexFromString($activeSheet->getHighestColumn());
        $hrownum = $activeSheet->getHighestRow() - 1;

        $sliceIndex = 0;
        $valueRowIndexesList = [];

        // skip header cell set.
        $sliceIndex += $hcolnum;

        for ($i = 1; $i <= $hrownum; $i++) {
            $tmp = array_slice($cellCollection->getCoordinates(), $sliceIndex, $hcolnum);

            array_push($valueRowIndexesList, [$tmp[0], $tmp[1], $tmp[2]]);

            $sliceIndex += $hcolnum;
        }

        // reset all aggregated cells to empty string.
        foreach ($valueRowIndexesList as $rows) {
            $activeSheet->setCellValue($rows[0], '');
            $activeSheet->setCellValue($rows[1], '');
            $activeSheet->setCellValue($rows[2], '');
        }

        $baseSlice = array_slice($cellCollection->getCoordinates(), 0, $hcolnum);
        $baseSlice = array_map(
            function (string $elem) {
                return $elem[0];
            },
            [$baseSlice[0], $baseSlice[1], $baseSlice[2]]
        );

        foreach ($entities as $index => $entity) {
            $activeSheet->setCellValue(sprintf('%s%d', $baseSlice[0], 2 + $index), $entity->getId());
            $activeSheet->setCellValue(sprintf('%s%d', $baseSlice[1], 2 + $index), $entity->getName());
            $activeSheet->setCellValue(sprintf('%s%d', $baseSlice[2], 2 + $index), $entity->getIdentityNumber());
        }

        $writer = new Xlsx($spreadsheet);
        $stream = fopen('php://memory', 'r+');

        $writer->save($stream);
        rewind($stream);

        $pstream = new Stream($stream);
        $response = new Response(HttpCode::OK, null, $pstream);

        $response = $response
            ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('Content-Disposition', 'attachment; filename="output.xlsx"')
            ->withHeader('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(mixed ...$args): ResponseInterface
    {
        return $this->concreteCallable($args[0], $args[1]);
    }
}
