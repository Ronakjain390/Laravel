// rowManagement.js – Contains logic for adding/deleting rows.
export function addRow(panelUserColumnDisplayNames, rows) {
    const dynamicFields = panelUserColumnDisplayNames.reduce((acc, columnName) => {
        if (columnName !== '') {
            acc[columnName] = '';
        }
        return acc;
    }, {});

    const staticFields = {
        item: '',
        quantity: null,
        rate: null,
        tax: null,
        calculateTax: true,
        total: null,
    };

    const newRow = { ...dynamicFields, ...staticFields };
    rows.push(newRow);

    return newRow;
}

export function deleteRow(index, rows) {
    rows.splice(index, 1);
}


export function calculateTotal(row, calculateTax) {
    const quantity = parseFloat(row.quantity) || 0;
    const rate = parseFloat(row.rate) || 0;
    const tax = parseFloat(row.tax) || 0;

    // Calculate total without tax
    let total = quantity * rate;

    // Apply tax if enabled
    if (calculateTax) {
        total += (total * tax) / 100;
    }

    // Set total for the row
    row.total = total.toFixed(2);

    return row.total;
}

export function totalQuantity(rows) {
    return rows.reduce((sum, row) => sum + (parseFloat(row.quantity) || 0), 0);
}

export function totalAmountBeforeDiscount(rows) {
    return rows.reduce((sum, row) => sum + (parseFloat(row.total) || 0), 0);
}

export function updateTotals(context) {
    // Recalculate the total quantity
    context.totalQty = totalQuantity(context.rows);

    // Calculate total amount before any discount or round-off
    let totalAmount = totalAmountBeforeDiscount(context.rows);

    // Apply global discount
    const discount = parseFloat(context.discount) || 0;
    const discountAmount = (totalAmount * discount) / 100;
    totalAmount -= discountAmount;

    // Apply round-off if enabled
    context.roundOffAmount = 0;
    if (context.roundOff) {
        context.roundOffAmount = Math.round(totalAmount) - totalAmount;
        totalAmount = Math.round(totalAmount);
    }

    context.totalAmount = totalAmount.toFixed(2);

    // Update tax breakdown
    generateTaxBreakdown(context);

    // Convert to words
    context.totalAmountInWords = numberToIndianRupees(totalAmount);
}

export function generateTaxBreakdown(context) {
    let breakdown = '';
    const discount = parseFloat(context.discount) || 0;
    let showBreakdown = false;

    // Ensure selectedUserState uses authUserState if undefined or empty
    let selectedState = (typeof context.selectedUserState !== 'undefined' && context.selectedUserState.trim() !== '')
                        ? context.selectedUserState.trim().toLowerCase()
                        : context.authUserState.trim().toLowerCase();

    let authState = context.authUserState.trim().toLowerCase();

    // Determine if IGST should be applied
    const applyIGST = selectedState !== authState;

    // Aggregate data by tax rate
    const taxData = {};

    context.rows.forEach((row) => {
        const taxRate = parseFloat(row.tax) || 0;
        if (taxRate === 0) return; // Skip rows with zero tax rate

        const totalWithoutTax = parseFloat(row.total) / (1 + (taxRate / 100));
        const discountedTotal = totalWithoutTax - (totalWithoutTax * discount) / 100;
        const taxAmount = discountedTotal * (taxRate / 100);

        if (!taxData[taxRate]) {
            taxData[taxRate] = {
                totalWithoutTax: 0,
                discountedTotal: 0,
                taxAmount: 0
            };
        }

        taxData[taxRate].totalWithoutTax += totalWithoutTax;
        taxData[taxRate].discountedTotal += discountedTotal;
        taxData[taxRate].taxAmount += taxAmount;
    });

    // Generate breakdown HTML
    for (const [taxRate, data] of Object.entries(taxData)) {
        if (discount > 0 || data.totalWithoutTax > 0) {
            showBreakdown = true;
            breakdown += `
                <tr class="text-xs text-black">
                    <td class="text-right">Amount at ${taxRate}%:</td>
                    <td class="text-right">${data.totalWithoutTax.toFixed(2)}</td>
                </tr>
                ${discount > 0 ? `
                <tr class="text-xs text-black">
                    <td class="text-right">Discount at ${discount}%:</td>
                    <td class="text-right">${(data.totalWithoutTax * discount / 100).toFixed(2)}</td>
                </tr>
                <tr class="text-xs text-black">
                    <td class="text-right font-bold">Amount at ${discount}%:</td>
                    <td class="text-right font-bold">${data.discountedTotal.toFixed(2)}</td>
                </tr>
                ` : ''}
                ${data.totalWithoutTax > 0 ?
                    applyIGST ? `
                    <tr class="text-xs text-black">
                        <td class="text-right">IGST ${taxRate}%:</td>
                        <td class="text-right">${data.taxAmount.toFixed(2)}</td>
                    </tr>
                    ` : `
                    <tr class="text-xs text-black">
                        <td class="text-right">SGST ${(taxRate / 2).toFixed(2)}%:</td>
                        <td class="text-right">${(data.taxAmount / 2).toFixed(2)}</td>
                    </tr>
                    <tr class="text-xs text-black">
                        <td class="text-right">CGST ${(taxRate / 2).toFixed(2)}%:</td>
                        <td class="text-right">${(data.taxAmount / 2).toFixed(2)}</td>
                    </tr>
                    `
                : ''}
                <tr class="text-xs text-black">
                    <td colspan="2" style="height: 10px;"></td>
                </tr>
            `;
        }
    }

    // Handle case where only discount is applied
    if (discount > 0 && Object.keys(taxData).length === 0) {
        showBreakdown = true;
        const totalAmountBeforeDiscount = totalAmountBeforeDiscount(context.rows);
        breakdown += `
            <tr class="text-xs text-black">
                <td class="text-right">Total Amount:</td>
                <td class="text-right">${totalAmountBeforeDiscount.toFixed(2)}</td>
            </tr>
            <tr class="text-xs text-black">
                <td class="text-right">Discount at ${discount}%:</td>
                <td class="text-right">${(totalAmountBeforeDiscount * discount / 100).toFixed(2)}</td>
            </tr>
            <tr class="text-xs text-black">
                <td class="text-right font-bold">Amount at ${discount}%:</td>
                <td class="text-right font-bold">${(totalAmountBeforeDiscount - (totalAmountBeforeDiscount * discount / 100)).toFixed(2)}</td>
            </tr>
        `;
    }

    if (showBreakdown) {
        context.taxBreakdown = `
            <table class="w-full">
                ${breakdown}
            </table>
        `;
    } else {
        context.taxBreakdown = '';
    }
}

export function convertNumberToWords(number) {
    const words = {
        0: 'Zero',
        1: 'One',
        2: 'Two',
        3: 'Three',
        4: 'Four',
        5: 'Five',
        6: 'Six',
        7: 'Seven',
        8: 'Eight',
        9: 'Nine',
        10: 'Ten',
        11: 'Eleven',
        12: 'Twelve',
        13: 'Thirteen',
        14: 'Fourteen',
        15: 'Fifteen',
        16: 'Sixteen',
        17: 'Seventeen',
        18: 'Eighteen',
        19: 'Nineteen',
        20: 'Twenty',
        30: 'Thirty',
        40: 'Forty',
        50: 'Fifty',
        60: 'Sixty',
        70: 'Seventy',
        80: 'Eighty',
        90: 'Ninety'
    };

    if (number < 21) {
        return words[number];
    } else if (number < 100) {
        const tens = words[10 * Math.floor(number / 10)];
        const units = number % 10;
        return tens + (units ? ' ' + words[units] : '');
    } else if (number < 1000) {
        const hundreds = words[Math.floor(number / 100)] + ' Hundred';
        const remainder = number % 100;
        return hundreds + (remainder ? ' and ' + convertNumberToWords(remainder) : '');
    } else if (number < 100000) {
        const thousands = convertNumberToWords(Math.floor(number / 1000)) + ' Thousand';
        const remainder = number % 1000;
        return thousands + (remainder ? ' ' + convertNumberToWords(remainder) : '');
    } else if (number < 10000000) {
        const lakhs = convertNumberToWords(Math.floor(number / 100000)) + ' Lakh';
        const remainder = number % 100000;
        return lakhs + (remainder ? ' ' + convertNumberToWords(remainder) : '');
    } else {
        const crores = convertNumberToWords(Math.floor(number / 10000000)) + ' Crore';
        const remainder = number % 10000000;
        return crores + (remainder ? ' ' + convertNumberToWords(remainder) : '');
    }
}

export function numberToIndianRupees(number) {
    const amountInWords = convertNumberToWords(Math.floor(number));
    const decimalPart = Math.round((number - Math.floor(number)) * 100);

    if (decimalPart > 0) {
        const decimalInWords = convertNumberToWords(decimalPart);
        return amountInWords + ' Rupees and ' + decimalInWords + ' Paisa';
    } else {
        return amountInWords + ' Rupees';
    }
}
