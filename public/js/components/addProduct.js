/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/js/components/addProduct.js":
/*!***********************************************!*\
  !*** ./resources/js/components/addProduct.js ***!
  \***********************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   addProductToRow: () => (/* binding */ addProductToRow)\n/* harmony export */ });\nfunction addProductToRow(product, rows, panelUserColumnDisplayNames) {\n  var _this = this;\n  // Check if the product already exists in the rows\n  var targetRow = this.rows.find(function (row) {\n    return row.item === product.item_code;\n  });\n  if (targetRow) {\n    // If the product exists, update the quantity and other details\n    targetRow.quantity += product.qty || 1; // Increment the quantity\n    targetRow.rate = product.rate || targetRow.rate; // Update the rate if provided\n\n    // Update detailed product data if details exist\n    if (Array.isArray(product.details)) {\n      product.details.forEach(function (detail) {\n        var trimmedColumnName = detail.column_name.trim(); // Trim column name\n        var trimmedPanelColumnNames = _this.panelUserColumnDisplayNames.map(function (name) {\n          return name.trim();\n        }); // Ensure panelUserColumnDisplayNames are trimmed\n\n        if (trimmedPanelColumnNames.includes(trimmedColumnName)) {\n          targetRow[trimmedColumnName] = detail.column_value;\n        }\n      });\n    }\n  } else {\n    // If the product does not exist, find an empty row or add a new row\n    targetRow = this.rows.find(function (row) {\n      return !row.item && !row.quantity && !row.rate;\n    }) || this.addRow();\n\n    // Assign basic product data to the target row\n    targetRow.item = product.item_code;\n    targetRow.quantity = product.qty || 1; // Default to 1 if qty is undefined\n    targetRow.rate = product.rate || 0; // Default to 0 if rate is undefined\n\n    // Assign detailed product data if details exist\n    if (Array.isArray(product.details)) {\n      product.details.forEach(function (detail) {\n        var trimmedColumnName = detail.column_name.trim(); // Trim column name\n        if (_this.panelUserColumnDisplayNames.includes(trimmedColumnName)) {\n          targetRow[trimmedColumnName] = detail.column_value;\n        }\n      });\n    }\n\n    // Ensure all columns in panelUserColumnDisplayNames are populated\n    this.panelUserColumnDisplayNames.filter(function (columnName) {\n      return columnName.trim() !== '';\n    }) // Filter out empty or whitespace strings\n    .forEach(function (columnName) {\n      var trimmedColumnName = columnName.trim();\n      if (product[trimmedColumnName] !== undefined) {\n        targetRow[trimmedColumnName] = product[trimmedColumnName];\n      }\n    });\n  }\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9yZXNvdXJjZXMvanMvY29tcG9uZW50cy9hZGRQcm9kdWN0LmpzIiwibWFwcGluZ3MiOiI7Ozs7QUFBTyxTQUFTQSxlQUFlQSxDQUFDQyxPQUFPLEVBQUVDLElBQUksRUFBRUMsMkJBQTJCLEVBQUM7RUFBQSxJQUFBQyxLQUFBO0VBQzFFO0VBQ0EsSUFBSUMsU0FBUyxHQUFHLElBQUksQ0FBQ0gsSUFBSSxDQUFDSSxJQUFJLENBQUMsVUFBQUMsR0FBRztJQUFBLE9BQUlBLEdBQUcsQ0FBQ0MsSUFBSSxLQUFLUCxPQUFPLENBQUNRLFNBQVM7RUFBQSxFQUFDO0VBRXJFLElBQUlKLFNBQVMsRUFBRTtJQUNYO0lBQ0FBLFNBQVMsQ0FBQ0ssUUFBUSxJQUFJVCxPQUFPLENBQUNVLEdBQUcsSUFBSSxDQUFDLENBQUMsQ0FBQztJQUN4Q04sU0FBUyxDQUFDTyxJQUFJLEdBQUdYLE9BQU8sQ0FBQ1csSUFBSSxJQUFJUCxTQUFTLENBQUNPLElBQUksQ0FBQyxDQUFDOztJQUVqRDtJQUNBLElBQUlDLEtBQUssQ0FBQ0MsT0FBTyxDQUFDYixPQUFPLENBQUNjLE9BQU8sQ0FBQyxFQUFFO01BQ2hDZCxPQUFPLENBQUNjLE9BQU8sQ0FBQ0MsT0FBTyxDQUFDLFVBQUFDLE1BQU0sRUFBSTtRQUM5QixJQUFNQyxpQkFBaUIsR0FBR0QsTUFBTSxDQUFDRSxXQUFXLENBQUNDLElBQUksQ0FBQyxDQUFDLENBQUMsQ0FBQztRQUNyRCxJQUFNQyx1QkFBdUIsR0FBR2pCLEtBQUksQ0FBQ0QsMkJBQTJCLENBQUNtQixHQUFHLENBQUMsVUFBQUMsSUFBSTtVQUFBLE9BQUlBLElBQUksQ0FBQ0gsSUFBSSxDQUFDLENBQUM7UUFBQSxFQUFDLENBQUMsQ0FBQzs7UUFFM0YsSUFBSUMsdUJBQXVCLENBQUNHLFFBQVEsQ0FBQ04saUJBQWlCLENBQUMsRUFBRTtVQUNyRGIsU0FBUyxDQUFDYSxpQkFBaUIsQ0FBQyxHQUFHRCxNQUFNLENBQUNRLFlBQVk7UUFDdEQ7TUFDSixDQUFDLENBQUM7SUFDTjtFQUNKLENBQUMsTUFBTTtJQUNIO0lBQ0FwQixTQUFTLEdBQUcsSUFBSSxDQUFDSCxJQUFJLENBQUNJLElBQUksQ0FBQyxVQUFBQyxHQUFHO01BQUEsT0FBSSxDQUFDQSxHQUFHLENBQUNDLElBQUksSUFBSSxDQUFDRCxHQUFHLENBQUNHLFFBQVEsSUFBSSxDQUFDSCxHQUFHLENBQUNLLElBQUk7SUFBQSxFQUFDLElBQUksSUFBSSxDQUFDYyxNQUFNLENBQUMsQ0FBQzs7SUFFM0Y7SUFDQXJCLFNBQVMsQ0FBQ0csSUFBSSxHQUFHUCxPQUFPLENBQUNRLFNBQVM7SUFDbENKLFNBQVMsQ0FBQ0ssUUFBUSxHQUFHVCxPQUFPLENBQUNVLEdBQUcsSUFBSSxDQUFDLENBQUMsQ0FBQztJQUN2Q04sU0FBUyxDQUFDTyxJQUFJLEdBQUdYLE9BQU8sQ0FBQ1csSUFBSSxJQUFJLENBQUMsQ0FBQyxDQUFJOztJQUV2QztJQUNBLElBQUlDLEtBQUssQ0FBQ0MsT0FBTyxDQUFDYixPQUFPLENBQUNjLE9BQU8sQ0FBQyxFQUFFO01BQ2hDZCxPQUFPLENBQUNjLE9BQU8sQ0FBQ0MsT0FBTyxDQUFDLFVBQUFDLE1BQU0sRUFBSTtRQUM5QixJQUFNQyxpQkFBaUIsR0FBR0QsTUFBTSxDQUFDRSxXQUFXLENBQUNDLElBQUksQ0FBQyxDQUFDLENBQUMsQ0FBQztRQUNyRCxJQUFJaEIsS0FBSSxDQUFDRCwyQkFBMkIsQ0FBQ3FCLFFBQVEsQ0FBQ04saUJBQWlCLENBQUMsRUFBRTtVQUM5RGIsU0FBUyxDQUFDYSxpQkFBaUIsQ0FBQyxHQUFHRCxNQUFNLENBQUNRLFlBQVk7UUFDdEQ7TUFDSixDQUFDLENBQUM7SUFDTjs7SUFFQTtJQUNBLElBQUksQ0FBQ3RCLDJCQUEyQixDQUMzQndCLE1BQU0sQ0FBQyxVQUFBQyxVQUFVO01BQUEsT0FBSUEsVUFBVSxDQUFDUixJQUFJLENBQUMsQ0FBQyxLQUFLLEVBQUU7SUFBQSxFQUFDLENBQUM7SUFBQSxDQUMvQ0osT0FBTyxDQUFDLFVBQUFZLFVBQVUsRUFBSTtNQUNuQixJQUFNVixpQkFBaUIsR0FBR1UsVUFBVSxDQUFDUixJQUFJLENBQUMsQ0FBQztNQUMzQyxJQUFJbkIsT0FBTyxDQUFDaUIsaUJBQWlCLENBQUMsS0FBS1csU0FBUyxFQUFFO1FBQzFDeEIsU0FBUyxDQUFDYSxpQkFBaUIsQ0FBQyxHQUFHakIsT0FBTyxDQUFDaUIsaUJBQWlCLENBQUM7TUFDN0Q7SUFDSixDQUFDLENBQUM7RUFDVjtBQUVEIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vdGhlcGFyY2hpLy4vcmVzb3VyY2VzL2pzL2NvbXBvbmVudHMvYWRkUHJvZHVjdC5qcz85OWQ0Il0sInNvdXJjZXNDb250ZW50IjpbImV4cG9ydCBmdW5jdGlvbiBhZGRQcm9kdWN0VG9Sb3cocHJvZHVjdCwgcm93cywgcGFuZWxVc2VyQ29sdW1uRGlzcGxheU5hbWVzKXtcbiAvLyBDaGVjayBpZiB0aGUgcHJvZHVjdCBhbHJlYWR5IGV4aXN0cyBpbiB0aGUgcm93c1xuIGxldCB0YXJnZXRSb3cgPSB0aGlzLnJvd3MuZmluZChyb3cgPT4gcm93Lml0ZW0gPT09IHByb2R1Y3QuaXRlbV9jb2RlKTtcblxuIGlmICh0YXJnZXRSb3cpIHtcbiAgICAgLy8gSWYgdGhlIHByb2R1Y3QgZXhpc3RzLCB1cGRhdGUgdGhlIHF1YW50aXR5IGFuZCBvdGhlciBkZXRhaWxzXG4gICAgIHRhcmdldFJvdy5xdWFudGl0eSArPSBwcm9kdWN0LnF0eSB8fCAxOyAvLyBJbmNyZW1lbnQgdGhlIHF1YW50aXR5XG4gICAgIHRhcmdldFJvdy5yYXRlID0gcHJvZHVjdC5yYXRlIHx8IHRhcmdldFJvdy5yYXRlOyAvLyBVcGRhdGUgdGhlIHJhdGUgaWYgcHJvdmlkZWRcblxuICAgICAvLyBVcGRhdGUgZGV0YWlsZWQgcHJvZHVjdCBkYXRhIGlmIGRldGFpbHMgZXhpc3RcbiAgICAgaWYgKEFycmF5LmlzQXJyYXkocHJvZHVjdC5kZXRhaWxzKSkge1xuICAgICAgICAgcHJvZHVjdC5kZXRhaWxzLmZvckVhY2goZGV0YWlsID0+IHtcbiAgICAgICAgICAgICBjb25zdCB0cmltbWVkQ29sdW1uTmFtZSA9IGRldGFpbC5jb2x1bW5fbmFtZS50cmltKCk7IC8vIFRyaW0gY29sdW1uIG5hbWVcbiAgICAgICAgICAgICBjb25zdCB0cmltbWVkUGFuZWxDb2x1bW5OYW1lcyA9IHRoaXMucGFuZWxVc2VyQ29sdW1uRGlzcGxheU5hbWVzLm1hcChuYW1lID0+IG5hbWUudHJpbSgpKTsgLy8gRW5zdXJlIHBhbmVsVXNlckNvbHVtbkRpc3BsYXlOYW1lcyBhcmUgdHJpbW1lZFxuXG4gICAgICAgICAgICAgaWYgKHRyaW1tZWRQYW5lbENvbHVtbk5hbWVzLmluY2x1ZGVzKHRyaW1tZWRDb2x1bW5OYW1lKSkge1xuICAgICAgICAgICAgICAgICB0YXJnZXRSb3dbdHJpbW1lZENvbHVtbk5hbWVdID0gZGV0YWlsLmNvbHVtbl92YWx1ZTtcbiAgICAgICAgICAgICB9XG4gICAgICAgICB9KTtcbiAgICAgfVxuIH0gZWxzZSB7XG4gICAgIC8vIElmIHRoZSBwcm9kdWN0IGRvZXMgbm90IGV4aXN0LCBmaW5kIGFuIGVtcHR5IHJvdyBvciBhZGQgYSBuZXcgcm93XG4gICAgIHRhcmdldFJvdyA9IHRoaXMucm93cy5maW5kKHJvdyA9PiAhcm93Lml0ZW0gJiYgIXJvdy5xdWFudGl0eSAmJiAhcm93LnJhdGUpIHx8IHRoaXMuYWRkUm93KCk7XG5cbiAgICAgLy8gQXNzaWduIGJhc2ljIHByb2R1Y3QgZGF0YSB0byB0aGUgdGFyZ2V0IHJvd1xuICAgICB0YXJnZXRSb3cuaXRlbSA9IHByb2R1Y3QuaXRlbV9jb2RlO1xuICAgICB0YXJnZXRSb3cucXVhbnRpdHkgPSBwcm9kdWN0LnF0eSB8fCAxOyAvLyBEZWZhdWx0IHRvIDEgaWYgcXR5IGlzIHVuZGVmaW5lZFxuICAgICB0YXJnZXRSb3cucmF0ZSA9IHByb2R1Y3QucmF0ZSB8fCAwOyAgICAvLyBEZWZhdWx0IHRvIDAgaWYgcmF0ZSBpcyB1bmRlZmluZWRcblxuICAgICAvLyBBc3NpZ24gZGV0YWlsZWQgcHJvZHVjdCBkYXRhIGlmIGRldGFpbHMgZXhpc3RcbiAgICAgaWYgKEFycmF5LmlzQXJyYXkocHJvZHVjdC5kZXRhaWxzKSkge1xuICAgICAgICAgcHJvZHVjdC5kZXRhaWxzLmZvckVhY2goZGV0YWlsID0+IHtcbiAgICAgICAgICAgICBjb25zdCB0cmltbWVkQ29sdW1uTmFtZSA9IGRldGFpbC5jb2x1bW5fbmFtZS50cmltKCk7IC8vIFRyaW0gY29sdW1uIG5hbWVcbiAgICAgICAgICAgICBpZiAodGhpcy5wYW5lbFVzZXJDb2x1bW5EaXNwbGF5TmFtZXMuaW5jbHVkZXModHJpbW1lZENvbHVtbk5hbWUpKSB7XG4gICAgICAgICAgICAgICAgIHRhcmdldFJvd1t0cmltbWVkQ29sdW1uTmFtZV0gPSBkZXRhaWwuY29sdW1uX3ZhbHVlO1xuICAgICAgICAgICAgIH1cbiAgICAgICAgIH0pO1xuICAgICB9XG5cbiAgICAgLy8gRW5zdXJlIGFsbCBjb2x1bW5zIGluIHBhbmVsVXNlckNvbHVtbkRpc3BsYXlOYW1lcyBhcmUgcG9wdWxhdGVkXG4gICAgIHRoaXMucGFuZWxVc2VyQ29sdW1uRGlzcGxheU5hbWVzXG4gICAgICAgICAuZmlsdGVyKGNvbHVtbk5hbWUgPT4gY29sdW1uTmFtZS50cmltKCkgIT09ICcnKSAvLyBGaWx0ZXIgb3V0IGVtcHR5IG9yIHdoaXRlc3BhY2Ugc3RyaW5nc1xuICAgICAgICAgLmZvckVhY2goY29sdW1uTmFtZSA9PiB7XG4gICAgICAgICAgICAgY29uc3QgdHJpbW1lZENvbHVtbk5hbWUgPSBjb2x1bW5OYW1lLnRyaW0oKTtcbiAgICAgICAgICAgICBpZiAocHJvZHVjdFt0cmltbWVkQ29sdW1uTmFtZV0gIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICB0YXJnZXRSb3dbdHJpbW1lZENvbHVtbk5hbWVdID0gcHJvZHVjdFt0cmltbWVkQ29sdW1uTmFtZV07XG4gICAgICAgICAgICAgfVxuICAgICAgICAgfSk7XG4gfVxuXG59XG4iXSwibmFtZXMiOlsiYWRkUHJvZHVjdFRvUm93IiwicHJvZHVjdCIsInJvd3MiLCJwYW5lbFVzZXJDb2x1bW5EaXNwbGF5TmFtZXMiLCJfdGhpcyIsInRhcmdldFJvdyIsImZpbmQiLCJyb3ciLCJpdGVtIiwiaXRlbV9jb2RlIiwicXVhbnRpdHkiLCJxdHkiLCJyYXRlIiwiQXJyYXkiLCJpc0FycmF5IiwiZGV0YWlscyIsImZvckVhY2giLCJkZXRhaWwiLCJ0cmltbWVkQ29sdW1uTmFtZSIsImNvbHVtbl9uYW1lIiwidHJpbSIsInRyaW1tZWRQYW5lbENvbHVtbk5hbWVzIiwibWFwIiwibmFtZSIsImluY2x1ZGVzIiwiY29sdW1uX3ZhbHVlIiwiYWRkUm93IiwiZmlsdGVyIiwiY29sdW1uTmFtZSIsInVuZGVmaW5lZCJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./resources/js/components/addProduct.js\n");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval-source-map devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./resources/js/components/addProduct.js"](0, __webpack_exports__, __webpack_require__);
/******/ 	
/******/ })()
;