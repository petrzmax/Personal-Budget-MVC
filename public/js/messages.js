class Messages {

    static getBalanceNegativeMessage() {
        return '<h2 class="text-danger my-2">Uwaga! Wpadasz w długi!</h2>';
    }

    static getBalancePositiveMessage() {
        return '<h2 class="text-success my-2">Brawo! Wspaniale zarządzasz finansami!</h2>';
    }

    static getBalanceMessage(balance) {
        return `<h2>Twój bilans z wybranego przedziału czasu: ${balance} złotych</h2>`;
    }

    static getTableRow({ categoryName, categorySum }) {
        return `
        <tr>
            <td>${categoryName}</td>
            <td>${categorySum}</td>
        </tr>`
    }

    static getLimitPositiveMessage(value) {
        return `Możesz jeszcze wydać: ${value} zł`;
    }

    static getLimitNegativeMessage() {
        return 'Uwaga, przekroczyłeś limit!';
    }
}
