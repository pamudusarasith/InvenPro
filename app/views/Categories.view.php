<div class="body">
  <?php App\View::render("components/Navbar") ?>
  <?php App\View::render("components/Sidebar") ?>
  <div class="content">
    <div class="category-container">
      <div class="top-bar">
        <h1>Categories</h1>
        <button id="new-category-btn" class="btn btn-primary">
          <span class="material-symbols-rounded">add</span>New Category
        </button>
      </div>

      <div class="search-container">
        <div class="row search-bar">
          <span class="material-symbols-rounded">search</span>
          <input type="text" class="search-input" placeholder="Search categories">
        </div>
      </div>

      <table class="tbl">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Products</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <!-- Sample data -->
          <tr>
            <td>1</td>
            <td>Vegetables</td>
            <td>Fresh vegetables</td>
            <td>24</td>
            <td><span class="status-badge active">Active</span></td>
            <td>
              <span class="material-symbols-rounded action-icon">edit</span>
              <span class="material-symbols-rounded action-icon">delete</span>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td>Fruits</td>
            <td>Fresh fruits</td>
            <td>18</td>
            <td><span class="status-badge active">Active</span></td>
            <td>
              <span class="material-symbols-rounded action-icon">edit</span>
              <span class="material-symbols-rounded action-icon">delete</span>
            </td>
          </tr>
          <tr>
            <td>3</td>
            <td>Beverages</td>
            <td>Drinks and liquids</td>
            <td>32</td>
            <td><span class="status-badge active">Active</span></td>
            <td>
              <span class="material-symbols-rounded action-icon">edit</span>
              <span class="material-symbols-rounded action-icon">delete</span>
            </td>
          </tr>
          <tr>
            <td>4</td>
            <td>Dairy</td>
            <td>Milk and dairy products</td>
            <td>15</td>
            <td><span class="status-badge active">Active</span></td>
            <td>
              <span class="material-symbols-rounded action-icon">edit</span>
              <span class="material-symbols-rounded action-icon">delete</span>
            </td>
          </tr>
          <tr>
            <td>5</td>
            <td>Snacks</td>
            <td>Light refreshments</td>
            <td>45</td>
            <td><span class="status-badge active">Active</span></td>
            <td>
              <span class="material-symbols-rounded action-icon">edit</span>
              <span class="material-symbols-rounded action-icon">delete</span>
            </td>
          </tr>
          <tr>
            <td>6</td>
            <td>Meat</td>
            <td>Fresh meat products</td>
            <td>28</td>
            <td><span class="status-badge active">Active</span></td>
            <td>
              <span class="material-symbols-rounded action-icon">edit</span>
              <span class="material-symbols-rounded action-icon">delete</span>
            </td>
          </tr>
          <tr>
            <td>7</td>
            <td>Bakery</td>
            <td>Fresh baked goods</td>
            <td>36</td>
            <td><span class="status-badge active">Active</span></td>
            <td>
              <span class="material-symbols-rounded action-icon">edit</span>
              <span class="material-symbols-rounded action-icon">delete</span>
            </td>
          </tr>
        </tbody>
      </table>

      <?php App\View::render("components/CategoryForm") ?>

      <style>
        .tbl {
          width: 100%;
          border-collapse: separate;
          border-spacing: 0;
          margin: 25px 0;
          background: #fff;
          border-radius: 8px;
          box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .tbl thead th {
          background: #f8f9fa;
          padding: 16px;
          font-weight: 600;
          text-align: left;
          color: #2c3e50;
          border-bottom: 2px solid #edf2f7;
        }

        .tbl tbody tr {
          transition: all 0.2s ease;
        }

        .tbl tbody tr:hover {
          background-color: #f8f9fa;
          transform: translateY(-2px);
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .tbl td {
          padding: 16px;
          border-bottom: 1px solid #edf2f7;
          color: #4a5568;
        }

        .status-badge {
          padding: 6px 12px;
          border-radius: 20px;
          font-size: 0.85em;
          font-weight: 500;
        }

        .status-badge.active {
          background-color: #e3f8ef;
          color: #0d9f6e;
        }

        .action-icon {
          color: #718096;
          cursor: pointer;
          padding: 4px;
          margin: 0 4px;
          border-radius: 4px;
          transition: all 0.2s;
        }

        .action-icon:hover {
          background-color: #edf2f7;
          color: #2d3748;
        }

        .search-container {
          margin: 20px 0;
        }

        .search-bar {
          display: flex;
          align-items: center;
          background: white;
          border-radius: 8px;
          padding: 8px 16px;
          box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
          border: 1px solid #edf2f7;
        }

        .search-input {
          border: none;
          outline: none;
          width: 100%;
          padding: 8px;
          margin-left: 8px;
          font-size: 0.95em;
        }

        .btn {
          display: flex;
          align-items: center;
          gap: 8px;
          padding: 10px 20px;
          border: none;
          border-radius: 6px;
          cursor: pointer;
          font-weight: 500;
          transition: all 0.2s;
        }

        .btn-primary {
          background-color: #3182ce;
          color: white;
        }

        .btn-primary:hover {
          background-color: #2c5282;
          transform: translateY(-1px);
          box-shadow: 0 2px 6px rgba(49, 130, 206, 0.3);
        }

        .category-container {
          background-color: #f7fafc;
          padding: 30px;
          border-radius: 12px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .top-bar {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 30px;
        }

        .top-bar h1 {
          color: #2d3748;
          font-size: 1.8rem;
          font-weight: 600;
        }

        .content {
          background-color: #fff;
        }
      </style>
      <script>
        document.querySelector('.search-input').addEventListener('input', async (e) => {
          const query = e.target.value.toLowerCase();
          const rows = document.querySelectorAll('.tbl tbody tr');

          rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
          });
        });

        document.querySelectorAll('.action-icon').forEach(icon => {
          icon.addEventListener('click', (e) => {
            const action = e.target.textContent;
            const row = e.target.closest('tr');
            const id = row.cells[0].textContent;

            if (action === 'edit') {
              // Show edit form logic
            } else if (action === 'delete') {
              if (confirm('Are you sure you want to delete this category?')) {
                row.remove();
              }
            }
          });
        });

        document.getElementById('new-category-btn').addEventListener('click', () => {
          document.getElementById('category-form-modal').showModal();
        });
      </script>
    </div>
  </div>
</div>