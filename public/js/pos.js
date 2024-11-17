let script = function () {
  //store here list of variables
  this.orderItems = {};
  //store the totalorder amount
  this.totalOrderAmount = 0.0;

  //want to this product data push the database
  this.products = {
    32: {
      name: "Malibun Milk powder",
      stock: 15,
      price: 880.25,
    },
    33: {
      name: "𝖪𝗈𝗍𝗍𝗎𝗆𝖾𝖾 𝖭𝗈𝗈𝖽𝖾𝗅𝖾𝗌",
      stock: 3,
      price: 150.0,
    },
    34: {
      name: "𝖸𝗈𝗀𝗁𝗎𝗋𝗍",
      stock: 100,
      price: 70.0,
    },
    35: {
      name: "𝖲𝗎𝗌𝗍𝖺𝗀𝖾𝗇",
      stock: 10,
      price: 1670.25,
    },
    36: {
      name: "𝖲𝖺𝗎𝗌𝖺𝗀𝖾𝗌",
      stock: 5,
      price: 880.25,
    },
    37: {
      name: "𝖫𝗂𝗉𝗍𝗈𝗇 𝖳𝖾𝖺",
      stock: 15,
      price: 580.75,
    },
    38: {
      name: "𝖠𝗉𝗉𝗅𝖾𝗃𝗎𝗂𝖼𝖾",
      stock: 7,
      price: 750.25,
    },
    39: {
      name: "𝖣𝖾𝗍𝖾𝗋𝗀𝖾𝗇𝗍 𝖯𝗈𝗐𝖽𝖾𝗋",
      stock: 20,
      price: 240.0,
    },
    40: {
      name: "𝖬𝖺𝗒𝗈𝗇𝗇𝖺𝗂𝗌𝖾",
      stock: 13,
      price: 1070.0,
    },
  };

  this.registerEvents = function () {
    document.addEventListener("click", function (e) {
      let targetE1 = e.target;
      let targetE1classList = targetE1.classList;

      //if click is add to order
      //user click on product images, or product info
      let addToOrderClasses = ["productImage", "productName", "productPrice"];

      if (
        targetE1classList.contains("productImage") ||
        targetE1classList.contains("productName") ||
        targetE1classList.contains("productPrice")
      ) {
        // get the product id clicked.
        let productContainer = targetE1.closest(".productContainer");
        let pid = productContainer.dataset.pid;
        let productInfo = loadscript.products[pid];

        //show dialogbox
        //ask for quantity
        //Display product like name and price
        //if quantity is grater than current stock ,then alert throw error
        //if quantity is not inputted throw error

        let orderQty = parseInt(
          prompt(
            `Add to Order: ${productInfo.name} - Rs.${productInfo.price}\n\nEnter quantity:`
          )
        );

        if (isNaN(orderQty)) {
          loadscript.dialogError("Please type a valid order quantity.");
          return;
        }

        let curStock = productInfo.stock;
        if (orderQty > curStock) {
          loadscript.dialogError(
            `Quantity is higher than current stock. (Available: ${curStock})`
          );
          return;
        }

        // If everything is valid, you can proceed with order logic here.
        console.log("Order added successfully:", {
          productId: pid,
          quantity: orderQty,
        });

        loadscript.addToOrder(productInfo, pid, orderQty);
      }

      //delete order items
      if (
        targetE1classList.contains("deleteOrderItem") ||
        targetE1.closest(".deleteOrderItem")
      ) {
        let deleteButton = targetE1.closest(".deleteOrderItem");
        let productId = deleteButton.dataset.id;

        if (confirm(`Are you sure you want to delete this item?`)) {
          // Get the quantity ordered from the orderItems object
          let orderedQuantity = loadscript.orderItems[productId]["orderQty"];
          loadscript.products[productId]["stock"] += orderedQuantity;

          delete loadscript.orderItems[productId];
          loadscript.updateOrderItemTable(); // Refresh the order list
        }
      }
    });
  };

  //----------------------------------------------------------------------------------
  this.updateOrderItemTable = function () {
    loadscript.totalOrderAmount = 0.0;

    //refresh order list table
    let ordersContainer = document.querySelector(".pos-items");

    let html = ' <p class="itemNoData">No data</p>';

    //check if order items varibles is empty or not
    if (Object.keys(loadscript.orderItems).length) {
      let tableHtml = `
        <table class="table">
              <thead>
                   <tr>
                      <th>#</th>
                      <th>PRODUCT</th>
                      <th>PRICE(Rs)</th>
                      <th>QTY</th>
                      <th>AMOUNT(Rs)</th>
                      <th></th>
                   </tr>
             </thead>
             <tbody>__Rows__</tbody>
        </table>`;

      //Loop orderitems and store in rows
      let rows = "";
      let rownNum = 1;
      for (const [pid, orderItem] of Object.entries(loadscript.orderItems)) {
        rows += `
                   <tr>
                      <td>${rownNum}</td>
                      <td>${orderItem["name"]}</td>
                      <td>${orderItem["price"].toFixed(2)}</td>
                      <td>${orderItem["orderQty"]}</td>
                      <td>${orderItem["amount"].toFixed(2)}</td>
                      <td> <a href="javascript:void(0)" class = "editeOrderItem" onclick="editItem()"  ><span class="material-symbols-rounded">edit</span> </a> </td>
                      <td> 
                           <a href="javascript:void(0)" class = "deleteOrderItem" data-id="${pid}">
                                <span class="material-symbols-rounded" >delete</span> 
                           </a>
                      </td>
                   </tr>
          `;
        rownNum++;

        loadscript.totalOrderAmount += orderItem["amount"];
      }

      html = tableHtml.replace("__Rows__", rows);
    }

    //append to order list table
    ordersContainer.innerHTML = html;

    loadscript.updatTotalOrderAmount();
  };

  this.updatTotalOrderAmount = function () {
    document.querySelector(".item-total-value").innerHTML =
      "Rs. " + loadscript.totalOrderAmount.toFixed(2).toLocaleString();
  };

  //-----------------------------------------------------------
  this.addToOrder = function (productInfo, pid, orderQty) {
    //add to order List to table
    //check current orders(store invariable)
    let curItemIds = Object.keys(loadscript.orderItems);
    let totalAmount = productInfo["price"] * orderQty;
    //check if it's already added

    if (curItemIds.indexOf(pid) > -1) {
      //if added,just update the quantity (add qty)
      loadscript.orderItems[pid]["amount"] += totalAmount;
      loadscript.orderItems[pid]["orderQty"] += orderQty;

      console.log("exist");
    } else {
      //else,add directly

      loadscript.orderItems[pid] = {
        name: productInfo["name"],
        price: productInfo["price"],
        orderQty: orderQty,
        amount: totalAmount,
      };
    }
    //update quantity to the productinfo
    loadscript.products[pid]["stock"] -= orderQty;

    this.updateOrderItemTable();

    //refresh order list table

    //refresh the products varible for update stock
  };

  this.dialogError = function (message) {
    alert(`Error: ${message}`);
  };

  this.initialize = function () {
    this.registerEvents(); // Register all app events like click, change, etc.
  };
};

let loadscript = new script();
loadscript.initialize(); // Correctly call initialize on the instance
