<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Service;

use function count;

use const JSON_UNESCAPED_UNICODE;

final class BankStatementParser
{
    private array $transactions = [];

    private array $lines = [];

    private int $index = 0;

    private int $lineCount = 0;

    /**
     * Строка начинается с даты.
     */
    private function startsWithDate(string $input): bool
    {
        return (bool) preg_match('/^\d{2}\.\d{2}\.\d{4}\b/', $input);
    }//end startsWithDate()

    public function isFio(string $input): bool
    {
        $pattern = '/^
        [А-ЯЁ][а-яё]+          # Фамилия (с заглавной буквы)
        \s+                     # Разделитель
        [А-ЯЁ]\.\s*[А-ЯЁ]\.    # Инициалы с точками
    $/ux';

        return (bool) preg_match($pattern, $input);
    }//end isFio()

    /**
     * Парсинг банковского выписки.
     */
    public function parse(string $text): array
    {
        $this->transactions = [];
        $this->lines        = explode("\n", $text);
        $this->index        = 0;
        $this->lineCount    = count($this->lines);

        $this->skipHeaderLines();

        $this->parseList();

        return $this->transactions;
    }//end parse()

    private function skipHeaderLines(): void
    {
        while ($this->index < $this->lineCount && ! $this->startsWithDate($this->lines[$this->index])) {
            ++$this->index;
        }
    }//end skipHeaderLines()

    private function parseList(): void
    {
        while ($this->index < $this->lineCount) {
            if (!$this->startsWithDate($this->lines[$this->index])) {
                ++$this->index;
                continue;
            }

            $transaction = $this->parseTransactionBlock($this->lines[$this->index]);
            if (!$transaction) {
                ++$this->index;
                continue;
            }

            ++$this->index;
            $description = $this->parseDescription();
            $transaction['description'] = trim($transaction['description']." ({$description})");
            $this->transactions[]       = $transaction;
        }
    }//end parseList()

    private function parseDescription(): string
    {
        $descriptionPart = [];
        do {
            $line = $this->lines[$this->index];
            if ($this->filterLine($line)) {
                $descriptionPart[] = $line;
            }

            ++$this->index;
        } while ($this->index < $this->lineCount && ! $this->startsWithDate($this->lines[$this->index]));

        $description = implode(' ', $descriptionPart);

        return $this->cleanDescriptionLine($description);
    }//end parseDescription()

    private function cleanDescriptionLine(string $description): string
    {
        return mb_trim(
            preg_replace(
                [
                    '/^\d{2}\.\d{2}\.\d{4}[\s\t]*/u',
                    '/\s*Операция\s+по карте\s*\*+\d+.*$/u',
                ],
                '',
                $description
            )
        );
    }//end cleanDescriptionLine()

    private function filterLine(string $line): bool
    {
        if ('Продолжение на следующей странице' === $line) {
            $this->index += 11;

            return false;
        }

        if ($this->isFio($line)) {
            $this->index = $this->lineCount;

            return false;
        }

        return true;
    }//end filterLine()

    /**
     * Парсинг транзакции.
     */
    private function parseTransactionBlock(string $block): ?array
    {
        if (preg_match($this->buildPattern(), $block, $matches)) {
            return $this->normalizeTransaction($matches);
        }

        return null;
    }//end parseTransactionBlock()

    /**
     * Нормализация транзакции.
     */
    private function normalizeTransaction(array $matches): array
    {
        return [
            'date'        => $matches['date'],
            'time'        => $matches['time'] ?? null,
            'code'        => $matches['code'] ?? null,
            'description' => mb_trim($matches['description']),
            'amount'      => $this->normalizeAmount($matches['amount']),
            'balance'     => isset($matches['balance']) ? $this->normalizeAmount($matches['balance']) : null,
        ];
    }//end normalizeTransaction()

    /**
     * Нормализация суммы.
     */
    private function normalizeAmount(string $amount): string
    {
        return preg_replace(['/,/', '/\s+/'], ['.', ''], str_replace(["\xC2\xA0", '&nbsp;'], '', $amount));
    }//end normalizeAmount()

    /**
     * Паттерн для парсинга транзакции.
     */
    private function buildPattern(): string
    {
        return '/^
            (?<date>\d{2}\.\d{2}\.\d{4})  # Дата
            \s+
            (?<time>\d{2}:\d{2})          # Время
            \s+
            (?<code>\d+)                  # Код операции
            (?<description>[^\t]+)        # Описание
            \t
            (?<amount>[+-]?\s*[\d\s,]+)   # Сумма
            \t
            (?<balance>[\d\s,]+)          # Баланс
        $/ux';
    }//end buildPattern()

    private function display(mixed $text): void
    {
        echo json_encode($text, JSON_UNESCAPED_UNICODE).'<br>';
    }//end display()
}//end class
