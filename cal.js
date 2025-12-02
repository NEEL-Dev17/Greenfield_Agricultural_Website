class Calculator {
    constructor() {
        this.display = document.getElementById('display');
        this.currentInput = '0';
        this.previousInput = '';
        this.operation = null;
        this.resetScreen = false;
        
        this.initializeEventListeners();
    }
    
    initializeEventListeners() {

        document.querySelectorAll('[data-number]').forEach(button => {
            button.addEventListener('click', () => {
                this.appendNumber(button.getAttribute('data-number'));
            });
        });
        

        document.querySelectorAll('[data-action]').forEach(button => {
            button.addEventListener('click', () => {
                const action = button.getAttribute('data-action');
                this.handleAction(action);
            });
        });
        

        document.addEventListener('keydown', (event) => {
            this.handleKeyboardInput(event);
        });
    }
    
    appendNumber(number) {
        if (this.currentInput === '0' || this.resetScreen) {
            this.currentInput = number;
            this.resetScreen = false;
        } else {

            if (number === '.' && this.currentInput.includes('.')) {
                return;
            }
            this.currentInput += number;
        }
        this.updateDisplay();
    }
    
    handleAction(action) {
        switch(action) {
            case 'clear':
                this.clear();
                break;
            case 'delete':
                this.delete();
                break;
            case 'add':
            case 'subtract':
            case 'multiply':
            case 'divide':
                this.setOperation(action);
                break;
            case 'calculate':
                this.calculate();
                break;
        }
    }
    
    clear() {
        this.currentInput = '0';
        this.previousInput = '';
        this.operation = null;
        this.resetScreen = false;
        this.updateDisplay();
    }
    
    delete() {
        if (this.currentInput.length === 1) {
            this.currentInput = '0';
        } else {
            this.currentInput = this.currentInput.slice(0, -1);
        }
        this.updateDisplay();
    }
    
    setOperation(operation) {
        if (this.operation !== null) {
            this.calculate();
        }
        this.previousInput = this.currentInput;
        this.operation = operation;
        this.resetScreen = true;
    }
    
    calculate() {
        if (this.operation === null || this.resetScreen) return;
        
        let result;
        const prev = parseFloat(this.previousInput);
        const current = parseFloat(this.currentInput);
        
        if (isNaN(prev) || isNaN(current)) return;
        
        switch (this.operation) {
            case 'add':
                result = prev + current;
                break;
            case 'subtract':
                result = prev - current;
                break;
            case 'multiply':
                result = prev * current;
                break;
            case 'divide':
                if (current === 0) {
                    this.displayError("Cannot divide by zero!");
                    return;
                }
                result = prev / current;
                break;
            default:
                return;
        }
        

        result = Math.round(result * 100000000) / 100000000;
        
        this.currentInput = result.toString();
        this.operation = null;
        this.previousInput = '';
        this.resetScreen = true;
        this.updateDisplay();
    }
    
    displayError(message) {
        this.display.textContent = message;
        setTimeout(() => {
            this.updateDisplay();
        }, 2000);
    }
    
    updateDisplay() {
        let displayValue = this.currentInput;
    
        if (displayValue.includes('.')) {
            const parts = displayValue.split('.');
            parts[0] = this.formatNumberWithCommas(parts[0]);
            displayValue = parts.join('.');
        } else {
            displayValue = this.formatNumberWithCommas(displayValue);
        }
        
        this.display.textContent = displayValue;
    }
    
    formatNumberWithCommas(number) {
        return number.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    handleKeyboardInput(event) {
        if (event.key >= '0' && event.key <= '9') {
            this.appendNumber(event.key);
        } else if (event.key === '.') {
            this.appendNumber('.');
        } else if (event.key === '+') {
            this.setOperation('add');
        } else if (event.key === '-') {
            this.setOperation('subtract');
        } else if (event.key === '*') {
            this.setOperation('multiply');
        } else if (event.key === '/') {
            event.preventDefault();
            this.setOperation('divide');
        } else if (event.key === 'Enter' || event.key === '=') {
            this.calculate();
        } else if (event.key === 'Escape') {
            this.clear();
        } else if (event.key === 'Backspace') {
            this.delete();
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new Calculator();
});