#!/bin/bash

# Проверка, установлен ли Composer
if ! command -v composer &> /dev/null; then
    echo "Composer не установлен. Пожалуйста, установите Composer, чтобы продолжить."
    exit 1
fi

COMPOSER_BIN="php8.2 /usr/local/bin/composer"

FILE="composer.json"

if [ ! -f "$FILE" ]; then
    cat <<EOL > "$FILE"
{
    "name": "vendor/package",
    "description": "Only Linter",
    "version": "1.0.0",
    "require": {
        "php": "^8.2"
    }
}
EOL
    echo "Файл composer.json был создан."
fi

$COMPOSER_BIN config --no-plugins allow-plugins.dealerdirect/phpcodesniffer-composer-installer true

# Массив плагинов
declare -a plugins=(
    "ergebnis/composer-normalize" 
    "nunomaduro/phpinsights" 
    "friendsofphp/php-cs-fixer" 
    "nunomaduro/phpinsights" 
    "phpmd/phpmd" 
    "phpstan/phpstan" 
    "phpstan/phpstan-symfony" 
    "phpstan/phpstan-strict-rules" 
    "phpstan/phpstan-doctrine" 
    "thecodingmachine/phpstan-strict-rules" 
    "spaze/phpstan-disallowed-calls"
    "ergebnis/phpstan-rules"
    "phpstan/phpstan-deprecation-rules"
    "slam/phpstan-extensions ~6.4"
    "shipmonk/phpstan-rules"
    "shipmonk/dead-code-detector"
    "staabm/phpstan-todo-by"
    "nikic/php-parser"
    "roave/no-floaters"
    "squizlabs/php_codesniffer"
)

# Авто-добавление разрешений на выполнение плагинов в composer.json
# for plugin in "${plugins[@]}"; do
#     $COMPOSER_BIN config allow-plugins.$plugin true
# done

# Установка линтеров через Composer
echo "Установка пакетов для проверки PHP кода (linters)..."

$COMPOSER_BIN require --dev "${plugins[@]}" --no-interaction

# Проверка успешности установки
if [ $? -eq 0 ]; then
    echo "Пакеты успешно установлены."
else
    echo "Произошла ошибка при установке пакетов."
    exit 1
fi

# Создание директории .linter, если она не существует
if [ ! -d ".linter" ]; then
    echo "Создание директории .linter..."
    mkdir .linter
fi

# Создание директории tmp, если она не существует
if [ ! -d "tmp" ]; then
    echo "Создание директории tmp..."
    mkdir tmp
fi

# Создание директории tmp/phpstan, если она не существует
if [ ! -d "tmp/phpstan" ]; then
    echo "Создание директории tmp/phpstan..."
    mkdir tmp/phpstan
fi

# Массив файлов настроек
declare -a config_files=("phpcs.xml.dist" "phpmd.ruleset.xml" "phpstan.dist.neon" ".php-cs-fixer.dist.php" "phpinsights.php")

# Исходная директория для настроек
source_dir="/var/www/.config/def"


# Копирование файлов настроек в директорию .linter
for file in "${config_files[@]}"; do
    if [ -f ".linter/$file" ]; then
        echo "Файл настроек $file уже существует в директории .linter."
    else
        echo "Копирование файла настроек $file в директорию .linter..."
        cp "$source_dir/$file" ".linter/"
        if [ $? -eq 0 ]; then
            echo "Файл $file скопирован."
        else
            echo "Произошла ошибка при копировании файла $file."
        fi
    fi
done

# Создание директории .githubtmp, если она не существует
if [ ! -d ".github" ]; then
    echo "Создание директории .github..."
    mkdir .github
fi

# Создание директории .github/workflows, если она не существует
if [ ! -d ".github/workflows" ]; then
    echo "Создание директории .github/workflows..."
    mkdir .github/workflows
fi

cp "$source_dir/linter.yml" ".github/workflows/"
if [ $? -eq 0 ]; then
    echo "Файл .github/workflows/linter.yml скопирован."
else
    echo "Произошла ошибка при копировании файла .github/workflows/linter.yml"
fi

cp "$source_dir/test.sh" "./"
if [ $? -eq 0 ]; then
    echo "Файл test.sh скопирован."
else
    echo "Произошла ошибка при копировании файла test.sh"
fi

echo "Готово! Вы можете запускать линтеры."
