<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Command;

use App\Service\CategoryDetector;
use App\Service\ReceiptExporter;
use App\Service\ReceiptImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:export-receipts', description: 'Экспорт чеков из «Мои чеки онлайн»')]
final class ExportReceiptsCommand extends Command
{
    public function __construct(
        private readonly ReceiptExporter $exporter,
        private readonly ?ReceiptImporter $importer = null,
    ) {
        parent::__construct();
    }// end __construct()

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->addArgument('dateFrom', InputArgument::REQUIRED, 'Дата начала (Y-m-d)')
            ->addArgument('dateTo', InputArgument::REQUIRED, 'Дата окончания (Y-m-d)')
            ->addOption('no-import', null, InputOption::VALUE_NONE, 'Не импортировать чеки в БД');
    }// end configure()

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dateFrom = $input->getArgument('dateFrom');
        $dateTo   = $input->getArgument('dateTo');

        $result = $this->exporter->export($dateFrom, $dateTo);
        $receipts = $result['receipts'];

        $output->writeln(sprintf('Экспортировано чеков: %d', count($receipts)));

        // Импорт в БД.
        if ($this->importer !== null && ! $input->getOption('no-import')) {
            $this->importReceipts($receipts, $output);
        }

        // Сохранение JSON в файл (по аналогии с оригиналом).
        $json = json_encode($receipts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents(sprintf('var/receipts_%s_%s.json', $dateFrom, $dateTo), $json);

        return Command::SUCCESS;
    }// end execute()

    /**
     * Импортировать чеки в БД.
     *
     * @param array<int, array<string, mixed>> $receipts
     */
    private function importReceipts(array $receipts, OutputInterface $output): void
    {
        $output->writeln('Импорт чеков в базу данных...');

        $batchSize = 50;
        $batch = [];
        $imported = 0;
        $skipped = 0;

        foreach ($receipts as $receipt) {
            $key = $receipt['id'] ?? null;
            $kktOwner = $receipt['name_short'] ?? null;

            $batch[] = [
                'data'     => $receipt,
                'key'      => $key,
                'kktOwner' => $kktOwner,
            ];

            if (count($batch) >= $batchSize) {
                $results = $this->importer->importBatch($batch);

                foreach ($results as $result) {
                    if ($result !== null) {
                        ++$imported;
                    } else {
                        ++$skipped;
                    }
                }

                $batch = [];
            }
        }// end foreach

        // Обрабатываем оставшиеся чеки.
        if (! empty($batch)) {
            $results = $this->importer->importBatch($batch);

            foreach ($results as $result) {
                if ($result !== null) {
                    ++$imported;
                } else {
                    ++$skipped;
                }
            }
        }

        $output->writeln(
            sprintf(
                'Импорт завершён: %d импортировано, %d пропущено',
                $imported,
                $skipped,
            )
        );
    }// end importReceipts()
}// end class
