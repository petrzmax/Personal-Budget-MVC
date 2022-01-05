class Messages {

    static get negative() {
        return '<h2 class="text-danger my-2">Uwaga! Wpadasz w długi!</h2>';
    }

    static get positive() {
        return '<h2 class="text-success my-2">Brawo! Wspaniale zarządzasz finansami!</h2>';
    }

    static getBalanceMessage(balance) {
        return `<h2>Twój bilans z wybranego przedziału czasu: ${balance} złotych</h2>`
    }

    static getTableRow({ categoryName, categorySum}) {
        return `
        <tr>
            <td>${categoryName}</td>
            <td>${categorySum}</td>
        </tr>`
    }
}
