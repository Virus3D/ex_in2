import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.element.addEventListener('change', this.handleChange.bind(this));
    }

    handleChange(event) {
        const value = event.target.value;

        // Если пользователь ввел новое значение, которое не в списке
        if (value && !this.isExistingOption(value)) {
            this.convertToTextInput(value);
        }
    }

    isExistingOption(value) {
        const options = Array.from(this.element.options);
        return options.some(option => option.value === value);
    }

    convertToTextInput(value) {
        const parent = this.element.parentNode;

        // Создаем новое текстовое поле
        const textInput = document.createElement('input');
        textInput.type = 'text';
        textInput.name = this.element.name;
        textInput.value = value;
        textInput.className = 'form-control';

        // Заменяем select на input
        parent.replaceChild(textInput, this.element);

        // Фокусируемся на новом поле
        textInput.focus();
    }
}
