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

/**
 * Парсер банковской выписки.
 *
 * Преобразует текстовое представление выписки (например, из интернет-банка)
 * в структурированный массив транзакций.
 */
final class BankStatementParser
{
    /**
     * Массив успешно распарсенных транзакций.
     *
     * @var array<mixed>
     */
    private array $transactions = [];

    /**
     * Строки исходного текста выписки.
     *
     * @var string[]
     */
    private array $lines = [];

    /**
     * Текущий индекс строки при парсинге.
     */
    private int $index = 0;

    /**
     * Общее количество строк в выписке.
     */
    private int $lineCount = 0;

    /**
     * Проверяет, начинается ли строка с даты в формате ДД.ММ.ГГГГ.
     *
     * @param string $input Строка для проверки.
     *
     * @return bool true, если строка начинается с даты, иначе false.
     */
    private function startsWithDate(string $input): bool
    {
        return (bool) preg_match('/^\d{2}\.\d{2}\.\d{4}\b/', $input);
    }// end startsWithDate()

    /**
     * Определяет, является ли строка ФИО (фамилия + инициалы).
     *
     * Формат: Фамилия (с заглавной буквы, русские буквы) затем пробел,
     * затем инициалы: большая буква, точка, возможно пробел, большая буква, точка.
     *
     * @param string $input Строка для проверки.
     *
     * @return bool true, если строка соответствует формату ФИО.
     */
    public function isFio(string $input): bool
    {
        $pattern = '/^
        [А-ЯЁ][а-яё]+          # Фамилия (с заглавной буквы)
        \s+                    # Разделитель
        [А-ЯЁ]\.\s*[А-ЯЁ]\.    # Инициалы с точками
    $/ux';

        return (bool) preg_match($pattern, $input);
    }// end isFio()

    /**
     * Основной метод: парсинг текста банковской выписки.
     *
     * @param string $text Содержимое выписки в виде строки.
     *
     * @return array<mixed> Массив транзакций. Каждая транзакция содержит поля:
     *               date, time, code, description, amount, balance.
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
    }// end parse()

    /**
     * Пропускает заголовочные строки до первой строки с датой.
     */
    private function skipHeaderLines(): void
    {
        while ($this->index < $this->lineCount && ! $this->startsWithDate($this->lines[$this->index])) {
            ++$this->index;
        }
    }// end skipHeaderLines()

    /**
     * Разбирает все транзакции из подготовленного списка строк.
     *
     * Идёт по строкам, находит блоки, начинающиеся с даты,
     * извлекает транзакцию и добавляет её в массив.
     */
    private function parseList(): void
    {
        while ($this->index < $this->lineCount) {
            $currentLine = $this->lines[$this->index];
            // Пропускаем строки без даты в начале.
            if (!$this->startsWithDate($currentLine)) {
                ++$this->index;
                continue;
            }

            // Парсим саму транзакцию (дата, время, код, описание, сумма, баланс).
            $transaction = $this->parseTransactionBlock($currentLine);
            if (!$transaction) {
                ++$this->index;
                continue;
            }

            ++$this->index;
            // Парсим многострочное описание (продолжение транзакции на следующих строках).
            $description = $this->parseDescription();
            // Дополняем описание из блока детализации.
            $transaction['description'] = trim($transaction['description'] . " ({$description})");
            $this->transactions[]       = $transaction;
        }// end while
    }// end parseList()

    /**
     * Парсит описание транзакции, которое может занимать несколько строк.
     *
     * Собирает строки до тех пор, пока не встретит строку с новой датой
     * или не достигнет конца списка. Применяет фильтрацию ненужных строк.
     */
    private function parseDescription(): string
    {
        $descriptionPart = [];
        do {
            $line = $this->lines[$this->index];
            // Фильтруем строки, которые не должны попадать в описание.
            if ($this->filterLine($line)) {
                $descriptionPart[] = $line;
            }

            ++$this->index;
        } while ($this->index < $this->lineCount && ! $this->startsWithDate($this->lines[$this->index]));

        $description = implode(' ', $descriptionPart);

        return $this->cleanDescriptionLine($description);
    }// end parseDescription()

    /**
     * Очищает строку описания от служебных конструкций.
     *
     * Удаляет:
     * - дату в начале строки,
     * - фразу "Операция по карте ****..." и подобное.
     *
     * @param string $description Исходное описание.
     *
     * @return string Очищенное описание.
     */
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
    }// end cleanDescriptionLine()

    /**
     * Фильтрует строки при сборе описания.
     *
     * @param string $line Строка для проверки.
     *
     * @return bool true, если строку нужно включить в описание, иначе false.
     */
    private function filterLine(string $line): bool
    {
        // Если встретили маркер "Продолжение на следующей странице" — пропускаем 11 строк.
        if ('Продолжение на следующей странице' === $line) {
            $this->index += 11;

            return false;
        }

        // Если строка является ФИО — прекращаем парсинг (дошли до подписи/конца).
        if ($this->isFio($line)) {
            $this->index = $this->lineCount;

            return false;
        }

        return true;
    }// end filterLine()

    /**
     * Парсит одну строку-блок транзакции (дата, время, код, описание, сумма, баланс).
     *
     * @param string $block Строка с данными транзакции.
     *
     * @return array<mixed>|null Ассоциативный массив с полями транзакции или null, если не удалось распарсить.
     */
    private function parseTransactionBlock(string $block): ?array
    {
        if (preg_match($this->buildPattern(), $block, $matches)) {
            return $this->normalizeTransaction($matches);
        }

        return null;
    }// end parseTransactionBlock()

    /**
     * Нормализует данные транзакции, полученные из регулярного выражения.
     *
     * Приводит суммы к стандартному формату (точка как разделитель дробной части),
     * удаляет лишние пробелы.
     *
     * @param array<mixed> $matches Результаты поиска по шаблону.
     *
     * @return array<mixed> Нормализованная транзакция.
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
    }// end normalizeTransaction()

    /**
     * Преобразует сумму из строкового представления в нормализованный вид.
     *
     * Заменяет запятую на точку, удаляет пробелы и неразрывные пробелы.
     *
     * @param string $amount Строка с суммой (может содержать пробелы, запятую, знак + или -).
     *
     * @return string Нормализованная строка с точкой в качестве десятичного разделителя.
     */
    private function normalizeAmount(string $amount): string
    {
        return preg_replace(['/,/', '/\s+/'], ['.', ''], str_replace(["\xC2\xA0", '&nbsp;'], '', $amount));
    }// end normalizeAmount()

    /**
     * Формирует регулярное выражение для разбора строки транзакции.
     *
     * Ожидаемый формат строки:
     * ДД.ММ.ГГГГ ЧЧ:ММ КОД ОПИСАНИЕ\tСУММА\tБАЛАНС
     *
     * @return string Регулярное выражение с именованными подмасками.
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
    }// end buildPattern()

    /**
     * Вспомогательный метод для отладки: выводит данные в формате JSON с HTML-разрывом строки.
     * (Не используется в основной логике, оставлен для возможной отладки.)
     *
     * @param mixed $text Данные для отображения.
     */
    private function display(mixed $text): void
    {
        echo json_encode($text, JSON_UNESCAPED_UNICODE) . '<br>';
    }// end display()
}// end class
