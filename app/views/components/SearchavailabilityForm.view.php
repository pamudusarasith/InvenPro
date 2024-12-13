<dialog id="branch-form-modal" class="modal">
    <div class="modal-content">
        <div class="row">
            <span class="material-symbols-rounded modal-close-btn modal-close">close</span>
        </div>
        <h1 class="modal-header">Search Availability</h1>
        <form id="branch-form" action="/branch/new" method="post">
            <label for="prod-category">search Product</label>
            <div id="prod-search" class="search-container">
                <div class="row search-bar">
                    <span class="material-symbols-rounded">search</span>
                    <input type="text" class="" placeholder="Search Products">
                </div>
            </div>

            <div class="modal-error">
                <span class="material-symbols-rounded">error</span>
                <span id="error-msg" class="error-msg"></span>
            </div>
            <div class="row modal-action-btns">
                <span class="loader" style="margin: 24px 12px 0px; font-size: 12px"></span>
                <button type="submit" class="btn btn-primary">search</button>
            </div>
            <div id="branch-table" class="tbl" style="display: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Location</th>
                            <th>Quantity</th>
                            <th>Distance (km) </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Battaramulla</td>
                            <td>2</td>
                            <td>235</td>
                            <td>150</td>
                        </tr>
                        <tr>
                            <td>Biyagama</td>
                            <td>2</td>
                            <td>108</td>
                            <td>50</td>
                        </tr>
                        <tr>
                            <td>Kochchikade</td>
                            <td>5</td>
                            <td>110</td>
                            <td>550</td>
                        </tr>
                        <tr>
                            <td>Boralesgamuwa</td>
                            <td>1</td>
                            <td>169</td>
                            <td>1500</td>
                        </tr>
                        <tr>
                            <td>Athurugiriya</td>
                            <td>1</td>
                            <td>195</td>
                            <td>2</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</dialog>