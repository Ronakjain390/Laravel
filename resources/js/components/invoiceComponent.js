export function invoiceComponent(authUserStateFromServer, panelUserColumnDisplayNames, rowsFromServer, context, selectUserFromLivewire, unitsFromServer) {
    console.log('Units received:', unitsFromServer);
    return {
        rows: rowsFromServer,
        productCode: '',
        panelUserColumnDisplayNames: panelUserColumnDisplayNames,
        authUserState: authUserStateFromServer,
        selectUser: selectUserFromLivewire,
        calculateTax: true,
        discount: null,
        roundOff: true,
        taxBreakdown: '',
        selectedProducts: [],
        totalAmountInWords: '',
        totalQty: null,
        totalAmount: null,
        roundOffAmount: null,
        showAlert: false,
        barcode: '',
        productData: {},
        checked: [],
        context: context,
        inputsDisabled: false,
        articleError: false,
        articleErrorMessage: '',
        rateError: false,
        articleErrors: {},
        rateErrors: {},
        rateErrorMessage: '',
        units: unitsFromServer || [],
        allChecked: false,
        selectPage: false,
        selectAll: false,

        toggleAll() {
            this.allChecked = !this.allChecked;

            const checkboxes = document.querySelectorAll('.product-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.allChecked;

                // Handle the checked array
                if (this.allChecked) {
                    if (!this.checked.includes(checkbox.value)) {
                        this.checked.push(checkbox.value);
                    }
                } else {
                    this.checked = this.checked.filter(item => item !== checkbox.value);
                }
            });

            this.selectPage = this.allChecked;
        },


            selectUserFromOthers() {
                // Function to handle "Others" button click
                this.selectedUserState = this.authUserState;  // Use authUserState if "Others" is clicked
                this.selectUser = true;
            },
            // Function to handle dynamic text based on context
            getDynamicText(taxRate) {
                // Check if the context is 'invoice' or 'challan'
                if (this.context === 'invoice') {
                    return `Sales at`;
                } else {
                    return `Amount at`;
                }
            },



            init() {
                // Listen for the 'productFound' event from Livewire
                Livewire.on('productFound', (product) => {
                    this.showAlert = false; // Hide alert if product is found
                    this.barcode = '';
                    this.addProductToRow(product);
                });

                // Listen for the 'productNotFound' event from Livewire
                Livewire.on('productNotFound', () => {
                    this.showAlert = true; // Show alert if product is not found
                    this.reinitializeAlpine();
                });

                // Add event listener for Livewire updates
                document.addEventListener('livewire:load', function () {
                    Livewire.hook('message.processed', (message, component) => {
                        if (component.el.id === 'invoice-component') {
                            Alpine.initializeComponent(component.el);
                        }
                    });
                });

                // Add listener for checkbox changes
                document.addEventListener('change', (event) => {
                    if (event.target.classList.contains('product-checkbox')) {
                        this.handleCheckboxChange(event, event.target.value);
                    }
                });
            },
            reinitializeComponent() {
                // Reinitialize Alpine bindings
                this.$nextTick(() => {
                    this.rows = [...this.rows];
                    this.updateTotals();
                });
            },


            addProductToRow(product) {
                // Check if the product already exists in the rows
                let targetRow = this.rows.find(row => row.item_code === product.item_code);

                if (targetRow) {
                    // If the product exists, update the quantity and other details
                    targetRow.quantity += product.qty || 1; // Increment the quantity
                    targetRow.rate = product.rate || targetRow.rate; // Update the rate if provided
                    targetRow.item_code = product.item_code; // Ensure item_code is updated

                    // Update detailed product data if details exist
                    if (Array.isArray(product.details)) {
                        product.details.forEach(detail => {
                            const trimmedColumnName = detail.column_name.trim();
                            const trimmedPanelColumnNames = this.panelUserColumnDisplayNames.map(name => name.trim());

                            if (trimmedPanelColumnNames.includes(trimmedColumnName)) {
                                targetRow[trimmedColumnName] = detail.column_value;
                            }
                        });
                    }
                } else {
                    // If the product does not exist, find an empty row or add a new row
                    targetRow = this.rows.find(row => !row.item_code && !row.quantity && !row.rate) || this.addRow();

                    // Assign basic product data to the target row
                    targetRow.item_code = product.item_code; // Ensure item_code is assigned
                    targetRow.quantity = product.qty || 1;
                    targetRow.rate = product.rate || 0;

                    // Assign detailed product data if details exist
                    if (Array.isArray(product.details)) {
                        product.details.forEach(detail => {
                            const trimmedColumnName = detail.column_name.trim();
                            if (this.panelUserColumnDisplayNames.includes(trimmedColumnName)) {
                                targetRow[trimmedColumnName] = detail.column_value;
                            }
                        });
                    }

                    // Ensure all columns in panelUserColumnDisplayNames are populated
                    this.panelUserColumnDisplayNames
                        .filter(columnName => columnName.trim() !== '')
                        .forEach(columnName => {
                            const trimmedColumnName = columnName.trim();
                            if (product[trimmedColumnName] !== undefined) {
                                targetRow[trimmedColumnName] = product[trimmedColumnName];
                            }
                        });
                }

                // Calculate total for the target row
                this.calculateTotal(targetRow);

                // Log the updated row for debugging
                console.log('Updated row:', targetRow);
            },
            addRow() {
                console.log(this.panelUserColumnDisplayNames, 'panelUserColumnDisplayNames');
                const dynamicFields = this.panelUserColumnDisplayNames.reduce((acc, columnName) => {
                    if (columnName !== '') {
                        acc[columnName] = '';
                    }
                    return acc;
                }, {});

                const staticFields = {
                    item_code: null,
                    quantity: null,
                    item_code: null,
                    rate: null,
                    tax: null,
                    calculateTax: true,
                    total: null,
                };

                const newRow = {
                    ...dynamicFields,
                    ...staticFields
                };

                this.rows.push(newRow);
                return newRow;
            },

            addSelectedProducts() {
                this.selectedProducts.forEach(product => {
                    const emptyRow = this.rows.find(row => !row.quantity && !row.rate && !row.item_code);

                    if (emptyRow) {
                        emptyRow.item_code = product.item_code;
                        emptyRow.quantity = product.qty;
                        emptyRow.rate = product.rate;
                        emptyRow.total = product.qty * product.rate;
                    } else {
                        const newRow = {
                            item_code: product.item_code,
                            quantity: product.qty,
                            rate: product.rate,
                            tax: 0,
                            calculateTax: true,
                            total: product.qty * product.rate
                        };
                        this.rows.push(newRow);
                    }
                });
            },

            deleteRow(index) {
                this.rows.splice(index, 1);
                this.updateTotals();
            },

            calculateTotal(row) {
                const quantity = parseFloat(row.quantity) || 0;
                const rate = parseFloat(row.rate) || 0;
                const tax = parseFloat(row.tax) || 0;

                // Calculate total without tax
                let total = quantity * rate;

                // Apply tax if enabled
                if (this.calculateTax) {
                    total += (total * tax) / 100;
                }

                // Set total for the row
                row.total = total.toFixed(2);

                // Recalculate totals
                this.updateTotals();

                return row.total;
            },

            totalQuantity() {
                return this.rows.reduce((sum, row) => sum + (parseFloat(row.quantity) || 0), 0);
            },

            totalAmountBeforeDiscount() {
                return this.rows.reduce((sum, row) => sum + (parseFloat(row.total) || 0), 0);
            },

            updateTotals() {
                // Recalculate the total quantity
                this.totalQty = this.totalQuantity();

                // Calculate total amount before any discount or round-off
                let totalAmount = this.totalAmountBeforeDiscount();

                // Apply global discount
                const discount = parseFloat(this.discount) || 0;
                const discountAmount = (totalAmount * discount) / 100;
                totalAmount -= discountAmount;

               // Apply round-off if enabled
               this.roundOffAmount = 0;
                        if (this.roundOff) {
                            this.roundOffAmount = Math.round(totalAmount) - totalAmount;
                            totalAmount = Math.round(totalAmount);
                }

                this.totalAmount = totalAmount.toFixed(2);

                // Update tax breakdown
                this.generateTaxBreakdown();

               // Convert to words
               this.totalAmountInWords = this.numberToIndianRupees(totalAmount);
            },

            generateTaxBreakdown() {
            let breakdown = '';
            const discount = parseFloat(this.discount) || 0;
            let showBreakdown = false;

            // Ensure selectedUserState uses authUserState if undefined or empty
            let selectedState = (typeof selectedUserState !== 'undefined' && selectedUserState.trim() !== '')
                                ? selectedUserState.trim().toLowerCase()
                                : this.authUserState.trim().toLowerCase();

            let authState = this.authUserState.trim().toLowerCase();

            // Determine if IGST should be applied
            const applyIGST = selectedState !== authState;

            // Aggregate data by tax rate
            const taxData = {};

            this.rows.forEach((row) => {
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
                                        <td class="text-right">${this.getDynamicText(taxRate)} ${taxRate}%:</td>
                                        <td class="text-right">${data.totalWithoutTax.toFixed(2)}</td>
                                    </tr>
                                    ${discount > 0 ? `
                                    <tr class="text-xs text-black">
                                        <td class="text-right">Discount at ${discount}%:</td>
                                        <td class="text-right">${(data.totalWithoutTax * discount / 100).toFixed(2)}</td>
                                    </tr>
                                    <tr class="text-xs text-black">
                                        <td class="text-right font-bold">${this.getDynamicText(taxRate)} ${discount}%:</td>
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
                            const totalAmountBeforeDiscount = this.totalAmountBeforeDiscount();
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
                            this.taxBreakdown = `
                                <table class="w-full">
                                    ${breakdown}
                                </table>
                            `;
                        } else {
                            this.taxBreakdown = '';
                        }
                    },

                    convertNumberToWords(number) {
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
                            return hundreds + (remainder ? ' and ' + this.convertNumberToWords(remainder) : '');
                        } else if (number < 100000) {
                            const thousands = this.convertNumberToWords(Math.floor(number / 1000)) + ' Thousand';
                            const remainder = number % 1000;
                            return thousands + (remainder ? ' ' + this.convertNumberToWords(remainder) : '');
                        } else if (number < 10000000) {
                            const lakhs = this.convertNumberToWords(Math.floor(number / 100000)) + ' Lakh';
                            const remainder = number % 100000;
                            return lakhs + (remainder ? ' ' + this.convertNumberToWords(remainder) : '');
                        } else {
                            const crores = this.convertNumberToWords(Math.floor(number / 10000000)) + ' Crore';
                            const remainder = number % 10000000;
                            return crores + (remainder ? ' ' + this.convertNumberToWords(remainder) : '');
                        }
                    },

                    numberToIndianRupees(number) {
                        const amountInWords = this.convertNumberToWords(Math.floor(number));
                        const decimalPart = Math.round((number - Math.floor(number)) * 100);

                        if (decimalPart > 0) {
                            const decimalInWords = this.convertNumberToWords(decimalPart);
                            return amountInWords + ' Rupees and ' + decimalInWords + ' Paisa';
                        } else {
                            return amountInWords + ' Rupees';
                        }
                    },
                    validateArticle(row) {
                        if (!row.Article || row.Article.trim() === '') {
                            this.articleErrors[row.id] = 'Required';
                        } else {
                            delete this.articleErrors[row.id];
                        }
                    },

                    validateRate(row) {
                        if (!row.rate || isNaN(parseFloat(row.rate)) || parseFloat(row.rate) <= 0) {
                            this.rateErrors[row.id] = 'Required';
                        } else {
                            delete this.rateErrors[row.id];
                        }
                    },

                    validateRow(row) {
                        this.validateArticle(row);
                        this.validateRate(row);
                    },

                    validateAllRows() {
                        this.rows.forEach(row => this.validateRow(row));
                    },
                    isFormValid() {
                        return Object.keys(this.articleErrors).length === 0 && Object.keys(this.rateErrors).length === 0;
                    },

                    draftData() {
                        const formattedData = this.rows.map(row => {
                            const columns = Object.entries(row)
                                .filter(([key, _]) => this.panelUserColumnDisplayNames.includes(key))
                                .map(([key, value]) => ({
                                    column_name: key,
                                    column_value: value
                                }));

                            return {
                                p_id: row.item_code || '',
                                unit: row.unit || null,
                                rate: parseFloat(row.rate) || null,
                                qty: parseFloat(row.quantity) || null,
                                round_off: null,
                                discount: null,
                                total_amount: parseFloat(row.total) || null,
                                tax_percentage: parseFloat(row.tax) || null,
                                discount_total_amount: null,
                                tax_amount: null,
                                tax: parseFloat(row.tax) || null,
                                item_code: row.item_code || null,
                                columns: columns
                            };
                        });
                        const requestData = {
                            order_details: formattedData,
                            total_qty: this.totalQty,
                            total: this.totalAmount,
                            discount_total_amount: this.discount
                        };
                        this.$wire.draftRows(requestData);
                    },
            submitData() {
                const formattedData = this.rows.map(row => {
                    const columns = Object.entries(row)
                        .filter(([key, _]) => this.panelUserColumnDisplayNames.includes(key))
                        .map(([key, value]) => ({
                            column_name: key,
                            column_value: value
                        }));

                    return {
                        p_id: row.item_code || '',
                        unit: row.unit || null,
                        rate: parseFloat(row.rate) || null,
                        qty: parseFloat(row.quantity) || null,
                        round_off: null,
                        discount: null,
                        total_amount: parseFloat(row.total) || null,
                        tax_percentage: parseFloat(row.tax) || null,
                        discount_total_amount: null,
                        tax_amount: null,
                        tax: parseFloat(row.tax) || null,
                        item_code: row.item_code || null,
                        columns: columns
                    };
                });

                const requestData = {
                    order_details: formattedData,
                    total_qty: this.totalQty,
                    total: this.totalAmount,
                    discount_total_amount: this.discount
                };

                this.$wire.saveRows(requestData);
            },

            editData() {
                const formattedData = this.rows.map(row => {
                    const columns = Object.entries(row)
                        .filter(([key, _]) => this.panelUserColumnDisplayNames.includes(key))
                        .map(([key, value]) => ({
                            column_name: key,
                            column_value: value
                        }));

                    return {
                        p_id: row.item_code || '',
                        unit: null,
                        rate: parseFloat(row.rate) || null,
                        qty: parseFloat(row.quantity) || null,
                        round_off: null,
                        discount: null,
                        total_amount: parseFloat(row.total) || null,
                        tax_percentage: parseFloat(row.tax) || null,
                        discount_total_amount: null,
                        tax_amount: null,
                        tax: parseFloat(row.tax) || null,
                        item_code: null,
                        columns: columns
                    };
                });

                const requestData = {
                    order_details: formattedData,
                    total_qty: this.totalQty,
                    total: this.totalAmount,
                    discount_total_amount: this.discount
                };

                this.$wire.editRows(requestData);
            },


             // Updated addSelectedDataToInputs for multiple select
        addSelectedDataToInputs() {
            if (!this.checked.length) {
                console.log('No items selected');
                return;
            }

            // Process each checked item
            this.checked.forEach(checkedItem => {
                try {
                    const productData = JSON.parse(checkedItem);
                    console.log('Processing product:', productData);

                    // Check if product already exists
                    let targetRow = this.rows.find(row => row.item_code === productData.item_code);

                    if (targetRow) {
                        console.log(`Product ${productData.item_code} already exists, skipping`);
                        return;
                    }

                    // Find empty row or create new one
                    targetRow = this.rows.find(row => !row.item_code && !row.quantity && !row.rate) || this.addRow();

                    // Basic data assignment
                    targetRow.item_code = productData.item_code;
                    targetRow.quantity = productData.qty || 1;
                    targetRow.rate = productData.rate || 0;

                    // Process detailed product data
                    if (Array.isArray(productData.details)) {
                        productData.details.forEach(detail => {
                            const trimmedColumnName = detail.column_name.trim();
                            if (this.panelUserColumnDisplayNames.includes(trimmedColumnName)) {
                                targetRow[trimmedColumnName] = detail.column_value;
                            }
                        });
                    }

                    // Ensure all panel columns are populated
                    this.panelUserColumnDisplayNames
                        .filter(columnName => columnName.trim() !== '')
                        .forEach(columnName => {
                            const trimmedColumnName = columnName.trim();
                            if (productData[trimmedColumnName] !== undefined) {
                                targetRow[trimmedColumnName] = productData[trimmedColumnName];
                            }
                        });

                    // Handle unit assignment
                    if (productData.unit) {
                        const unitShortName = productData.unit.toUpperCase();
                        if (this.units.length === 0 || !this.units.some(unit => unit.short_name === unitShortName)) {
                            this.units.push({ short_name: unitShortName, unit: productData.unit });
                        }
                        targetRow.unit = unitShortName;
                    }

                    // Calculate totals
                    this.calculateTotal(targetRow);

                } catch (error) {
                    console.error('Error processing product:', error);
                }
            });

            // Clear selections and update UI
            this.checked = [];
            this.allChecked = false;
            this.selectPage = false;

            // Force re-render
            this.$nextTick(() => {
                this.rows = [...this.rows];
                this.updateTotals();
            });
        },
         // Helper method to handle individual checkbox changes
         handleCheckboxChange(event, value) {
            if (event.target.checked) {
                if (!this.checked.includes(value)) {
                    this.checked.push(value);
                }
            } else {
                this.checked = this.checked.filter(item => item !== value);
                this.allChecked = false;
            }

            // Update allChecked state based on if all checkboxes are checked
            const checkboxes = document.querySelectorAll('.product-checkbox');
            this.allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
        },


            logProductData() {
                console.log(this.productData);
            },

            sendData() {
                this.logProductData();
                const inputField = document.getElementById('productDataInput');
                inputField.value = JSON.stringify(this.productData);
            },
    };
}
