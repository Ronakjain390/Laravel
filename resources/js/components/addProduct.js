export function addProductToRow(product, rows, panelUserColumnDisplayNames){
 // Check if the product already exists in the rows
 let targetRow = this.rows.find(row => row.item === product.item_code);

 if (targetRow) {
     // If the product exists, update the quantity and other details
     targetRow.quantity += product.qty || 1; // Increment the quantity
     targetRow.rate = product.rate || targetRow.rate; // Update the rate if provided

     // Update detailed product data if details exist
     if (Array.isArray(product.details)) {
         product.details.forEach(detail => {
             const trimmedColumnName = detail.column_name.trim(); // Trim column name
             const trimmedPanelColumnNames = this.panelUserColumnDisplayNames.map(name => name.trim()); // Ensure panelUserColumnDisplayNames are trimmed

             if (trimmedPanelColumnNames.includes(trimmedColumnName)) {
                 targetRow[trimmedColumnName] = detail.column_value;
             }
         });
     }
 } else {
     // If the product does not exist, find an empty row or add a new row
     targetRow = this.rows.find(row => !row.item && !row.quantity && !row.rate) || this.addRow();

     // Assign basic product data to the target row
     targetRow.item = product.item_code;
     targetRow.quantity = product.qty || 1; // Default to 1 if qty is undefined
     targetRow.rate = product.rate || 0;    // Default to 0 if rate is undefined

     // Assign detailed product data if details exist
     if (Array.isArray(product.details)) {
         product.details.forEach(detail => {
             const trimmedColumnName = detail.column_name.trim(); // Trim column name
             if (this.panelUserColumnDisplayNames.includes(trimmedColumnName)) {
                 targetRow[trimmedColumnName] = detail.column_value;
             }
         });
     }

     // Ensure all columns in panelUserColumnDisplayNames are populated
     this.panelUserColumnDisplayNames
         .filter(columnName => columnName.trim() !== '') // Filter out empty or whitespace strings
         .forEach(columnName => {
             const trimmedColumnName = columnName.trim();
             if (product[trimmedColumnName] !== undefined) {
                 targetRow[trimmedColumnName] = product[trimmedColumnName];
             }
         });
 }

}
