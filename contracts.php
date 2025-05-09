<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.html");
    exit();
}

$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="./css/sidebar_1.css">
    <script>
  function preventBack(){window.history.forward();}
  setTimeout("preventBack()", 0);
  window.onunload=function(){null};
</script>
    <script> 
        document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('confirmUpdateBtn').addEventListener('click', function() {

            bootstrap.Modal.getInstance(document.getElementById('updateModal')).hide();

            const rows = document.querySelectorAll("#dataTable tr");
            const data = [];

            rows.forEach(row => {
                if (row.querySelector("td[colspan]")) return;

                const rowData = {
                    id: row.querySelector("td:nth-child(2) input").value,
                    pn: row.querySelector("td:nth-child(3) input").value,
                    desc: row.querySelector("td:nth-child(4) input").value,
                    vendor: row.querySelector("td:nth-child(5) input").value,
                    group: row.querySelector(".grp").value,
                    department: row.querySelector(".dept").value,
                    division: row.querySelector(".div").value,
                    cdate: row.querySelector("td:nth-child(9) input").value,
                    edate: row.querySelector("td:nth-child(10) input").value
                };
                data.push(rowData);
            });

            fetch("update-contracts-data.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                alert(result.message || "Records Updated");
                console.log(result);
                toggleEdit();
                fetchData();
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error updating data");
            });
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {

            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();

            const allCheckedIds = [];
            document.querySelectorAll("#dataTable input[type='checkbox']:checked").forEach(checkbox => {
                allCheckedIds.push(checkbox.value);
            });

            fetch("delete-contracts-data.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ ids: allCheckedIds })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert("Selected items deleted successfully!");
                    checkedIds.clear(); 
                    fetchData();
                } else {
                    alert("Error deleting items: " + result.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error deleting items");
            });
        });
    });
</script>
    <script>
        let checkedIds = new Set(); 
        let role = "<?php echo $role; ?>";
        let currentPage = 1;
        const rowsPerPage = 10;
        let allData = [];
        let editMode = false;

        function checkRole(){
            if(role === "user"){
                document.getElementById("updateBtn").style.display = "none";
                document.getElementById("deleteBtn").style.display = "none";
                document.getElementById("editBtn").style.display= "none";
                document.getElementById("admintab").style.display= "none";
                document.getElementById("addBtn").style.display= "none";
            }
            else if(role=="admin"){
                document.getElementById("updateBtn").style.display = "inline-block"; 
                document.getElementById("deleteBtn").style.display = "inline-block"; 
                document.getElementById("editBtn").style.display = "inline-block"; 
                document.getElementById("admintab").style.display= "inline-block";
                document.getElementById("addBtn").style.display= "inline-block";
            }
            else{
                document.getElementById("updateBtn").style.display = "inline-block"; 
                document.getElementById("deleteBtn").style.display = "inline-block"; 
                document.getElementById("editBtn").style.display = "inline-block"; 
                document.getElementById("admintab").style.display= "none";
                document.getElementById("addBtn").style.display= "inline-block";
            }
        }
        window.onload = checkRole;

        document.addEventListener('DOMContentLoaded', function() {
            // Main checkbox listener
            document.getElementById('main-checkbox').addEventListener('change', function() {
                const isChecked = this.checked;
                
                // Check/uncheck all items in allData (not just current page)
                allData.forEach(item => {
                    if (isChecked) {
                        checkedIds.add(item.id.toString());
                    } else {
                        checkedIds.delete(item.id.toString());
                    }
                });
                
                // Update current page display
                displayPage(currentPage);
                
                // Update button states
                document.getElementById("updateBtn").disabled = !isChecked;
                document.getElementById("deleteBtn").disabled = !isChecked;
            });

            // Individual checkbox listener (delegated to handle dynamic elements)
            document.addEventListener('change', function(e) {
                if (e.target && e.target.matches('#dataTable input[type="checkbox"]')) {
                    const checkbox = e.target;
                    const id = checkbox.value;

                    if (checkbox.checked) {
                        checkedIds.add(id);
                    } else {
                        checkedIds.delete(id);
                    }
                    
                    // Update main checkbox state
                    updateMainCheckboxState();
                    
                    // Update button states
                    const anyChecked = checkedIds.size > 0;
                    document.getElementById("updateBtn").disabled = !anyChecked;
                    document.getElementById("deleteBtn").disabled = !anyChecked;
                }
            });
        });

        function updateMainCheckboxState() {
            const visibleCheckboxes = document.querySelectorAll("#dataTable input[type='checkbox']");
            const mainCheckbox = document.getElementById("main-checkbox");
            
            if (visibleCheckboxes.length === 0) {
                mainCheckbox.checked = false;
                return;
            }
            
            // Check if all visible checkboxes are checked
            const allChecked = Array.from(visibleCheckboxes).every(checkbox => checkbox.checked);
            mainCheckbox.checked = allChecked;
        }

        function exportToCSV() {
            if (checkedIds.size === 0) {
                alert("Please select at least one item to export");
                return;
            }

            const checkedData = allData.filter(item => checkedIds.has(item.id.toString()));

            const headers = [
                "Project Name",
                "Description",
                "Vendor",
                "Group",
                "Department",
                "Division",
                "Contract Date",
                "End Date"
            ];

            const csvRows = [];
            checkedData.forEach(item => {
                const rowData = [
                    `"${item.pn || ''}"`,
                    `"${item.desc || ''}"`,
                    `"${item.vendor || ''}"`,
                    `"${item.group || ''}"`,
                    `"${item.department || ''}"`,
                    `"${item.division || ''}"`,
                    `'${item.cdate || ''}'`,
                    `'${item.edate || ''}'`
                ];
                csvRows.push(rowData.join(","));
            });

            const csvContent = [
                headers.join(","),
                ...csvRows
            ].join("\n");

            const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
            const url = URL.createObjectURL(blob);
            const link = document.createElement("a");
            link.setAttribute("href", url);
            link.setAttribute("download", "contracts_data_" + new Date().toISOString().slice(0,16) + ".csv");
            link.style.visibility = "hidden";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function toggleEdit() {
            editMode = !editMode;
            const inputs = document.querySelectorAll("#dataTable input, #dataTable select");
            const fileInputs = document.querySelectorAll("#dataTable input[type='file']");

            inputs.forEach(input => {
                if (input.readOnly !== true) { 
                    input.disabled = !editMode;
                }
            });

            fileInputs.forEach(input => {
                input.disabled = !editMode;
            });

            document.getElementById("editBtn").textContent = editMode ? "Cancel" : "Edit";
            document.getElementById("updateBtn").disabled = !editMode;
            document.getElementById("deleteBtn").disabled = !editMode;
        }

        function fetchData() {
            let searchTerm = document.getElementById("serialNum").value;

            let formData = new FormData();
            formData.append("search", searchTerm);

            fetch("get-data_contracts.php", { 
                method: "POST", 
                body: formData 
            })
            .then(response => response.json())
            .then(data => {
                if (!data || data.length === 0) {
                    document.getElementById("dataTable").innerHTML = 
                        '<tr><td colspan="10">No results found</td></tr>';
                    document.getElementById("paginationControls").style.display = "none";
                    return;
                }

                allData = data;
                currentPage = 1; 
                displayPage(currentPage);
                // Ensure pagination controls are visible when data is found
                document.getElementById("paginationControls").style.display = "block";
            })
            .catch(error => {
                console.error("Error:", error);
                document.getElementById("dataTable").innerHTML = 
                    '<tr><td colspan="10">'+error+'</td></tr>';
                document.getElementById("paginationControls").style.display = "none";
            });
        }

        function displayPage(page) {
            const startIndex = (page - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            const paginatedData = allData.slice(startIndex, endIndex);

            let html = '';
            paginatedData.forEach(item => {
                const isChecked = checkedIds.has(item.id.toString());
                html += `
                <tr>
                    <td style="border:none"><input type="checkbox" value="${item.id || ''}" ${isChecked ? 'checked' : ''} ${editMode ? '' : 'disabled'}></td>
                    <td style="border:none; display:none"><input type="text" value="${item.id || ''}" readonly></td>
                    <td style="border:none"><input type="text" value="${item.pn || ''}" ${editMode ? '' : 'disabled'}></td>
                    <td style="border:none"><input type="text" value="${item.desc || ''}" ${editMode ? '' : 'disabled'}></td>
                    <td style="border:none"><input type="text" value="${item.vendor || ''}" ${editMode ? '' : 'disabled'}></td>
                    <td style="border:none">
                        <select class="grp" onchange="updateDepartmentOptions(this)" ${editMode ? '' : 'disabled'}>
                            <option value=""></option>
                            <option value="CESRA" ${item.group === 'CESRA' ? 'selected' : ''}>CESRA</option>
                            <option value="NDOG" ${item.group === 'NDOG' ? 'selected' : ''}>NDOG</option>
                            <option value="Finance" ${item.group === 'Finance' ? 'selected' : ''}>Finance</option>
                            <option value="Customer Care" ${item.group === 'Customer Care' ? 'selected' : ''}>Customer Care</option>
                            <option value="Office of the President" ${item.group === 'Office of the President' ? 'selected' : ''}>Office of the President</option>
                        </select>
                    </td>
                    <td style="border:none">
                        <select class="dept" onchange="updateDivisionOptions(this)" ${editMode ? '' : 'disabled'}>
                            ${getDepartmentOptions(item.group, item.department)}
                        </select>
                    </td>
                    <td style="border:none">
                        <select class="div" ${editMode ? '' : 'disabled'}>
                            ${getDivisionOptions(item.department, item.division)}
                        </select>
                    </td>
                    <td style="border:none"><input type="date" value="${item.cdate || ''}" ${editMode ? '' : 'disabled'}></td>
                    <td style="border:none"><input type="date" value="${item.edate || ''}" ${editMode ? '' : 'disabled'}></td>
                    <td style="border:none">
                        <input type="file" accept=".jpg, .jpeg, .png, .pdf" ${editMode ? '' : 'disabled'}>
                        ${item.file_path ? `<a href="${item.file_path}" target="_blank">View File</a>` : ''}
                    </td>
                </tr>
                `;
            });

            document.getElementById("dataTable").innerHTML = html;
            updatePaginationControls();
            updateMainCheckboxState();
            
            // Ensure pagination controls stay visible
            document.getElementById("paginationControls").style.display = "block";
        }

        function updatePaginationControls() {
            const totalPages = Math.ceil(allData.length / rowsPerPage);
            const paginationInfo = document.getElementById("paginationInfo");
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");

            paginationInfo.textContent = `Page ${currentPage} of ${totalPages}`;
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages || totalPages === 0;
            
            // Show pagination controls only if there's more than one page
            document.getElementById("paginationControls").style.display = totalPages > 1 ? "block" : "none";
        }

        function goToPrevPage() {
            if (currentPage > 1) {
                currentPage--;
                displayPage(currentPage);
            }
        }

        function goToNextPage() {
            const totalPages = Math.ceil(allData.length / rowsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                displayPage(currentPage);
            }
        }

        function updateDepartmentOptions(selectElement) {
            const row = selectElement.closest('tr');
            const group = selectElement.value;
            const deptSelect = row.querySelector('.dept');
            const divSelect = row.querySelector('.div');

            deptSelect.innerHTML = getDepartmentOptions(group);
            divSelect.innerHTML = '<option value="">Select department first</option>';
        }

        function updateDivisionOptions(selectElement) {
            const row = selectElement.closest('tr');
            const department = selectElement.value;
            const divSelect = row.querySelector('.div');

            divSelect.innerHTML = getDivisionOptions(department);
        }

        function getDepartmentOptions(group, selectedDepartment = "") {
            if (!group) return '<option value="">Select group first</option>';

            const options = {
                "CESRA": [
                    {value: "CESRA", label: "CESRA"}
                ],
                "NDOG": [
                    {value: "NDOG", label: "NDOG"}
                ],
                "Finance": [
                    {value: "Controllership", label: "Controllership"},
                    {value: "Admin and General Services", label: "Admin and General Services"},
                    {value: "Revenue Management", label: "Revenue Management"}
                ],
                "Customer Care": [
                    {value: "Customer Care", label: "Customer Care"}
                ],
                "Office of the President": [
                    {value: "Office of the President", label: "Office of the President"}
                ]
            };

            let html = '<option value=""></option>';

            if (options[group]) {
                options[group].forEach(option => {
                    const selected = option.value === selectedDepartment ? 'selected' : '';
                    html += `<option value="${option.value}" ${selected}>${option.label}</option>`;
                });
            } else {
                html = '<option value="">Select group first</option>';
            }

            return html;
        }

        function getDivisionOptions(department, selectedDivision = "") {
            if (!department) return '<option value="">Select department first</option>';

            const options = {
                "CESRA": [
                    {value: "CESRA", label: "CESRA"}
                ],
                "Admin and General Services": [
                    {value: "Admin and Facilities", label: "Admin and Facilities"},
                    {value: "Logistics", label: "Logistics"}
                ],
                "NDOG": [
                    {value: "Health and Safety", label: "Health and Safety"},
                    {value: "Line Construction, Maintenance and Metering operations", label: "Line Construction, Maintenance and Metering operations"},
                    {value: "Power Systems Planning and Design Division", label: "Power Systems Planning and Design Division"},
                    {value: "Project Development and Management", label: "Project Development and Management"},
                    {value: "Sitio Electrification Program", label: "Sitio Electrification Program"},
                    {value: "System Loss Reduction Program", label: "System Loss Reduction Program"},
                    {value: "System Operations", label: "System Operations"},
                    {value: "Technical Services", label: "Technical Services"}
                ],
                "Revenue Management": [
                    {value: "Meter Read and Bill", label: "Meter Read and Bill"},
                    {value: "Billing and Connection", label: "Billing and Connection"},
                    {value: "Treasury", label: "Treasury"}
                ],
                "Customer Care": [
                    {value: "Customer Service", label: "Customer Service"},
                    {value: "Community Relations", label: "Community Relations"},
                    {value: "Key Accounts", label: "Key Accounts"},
                    {value: "Marketing", label: "Marketing"}
                ],
                "Controllership": [
                    {value: "Accounting", label: "Accounting"},
                    {value: "Information Technology", label: "Information Technology"}
                ],
                "Office of the President": [
                    {value: "Legal", label: "Legal"},
                    {value: "Human Capital Management", label: "Human Capital Management"},
                    {value: "Procurement", label: "Procurement"},
                    {value: "Security", label: "Security"}
                ]
            };

            let html = '<option value=""></option>';

            if (options[department]) {
                options[department].forEach(option => {
                    const selected = option.value === selectedDivision ? 'selected' : '';
                    html += `<option value="${option.value}" ${selected}>${option.label}</option>`;
                });
            } else {
                html = '<option value="">Select department first</option>';
            }

            return html;
        }

        function updateData() {
            const updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
            updateModal.show();
        }
        
        function deleteSelecteds() {
            const allCheckedIds = [];
            document.querySelectorAll("#dataTable input[type='checkbox']:checked").forEach(checkbox => {
                allCheckedIds.push(checkbox.value);
            });

            if (allCheckedIds.length === 0) {
                alert("Please select at least one item to delete");
                return;
            }

            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        function changeOptions() {
            const groupSelect = document.getElementById("group");
            const deptSelect = document.getElementById("dept");
            const selectedGroup = groupSelect.value;

            deptSelect.innerHTML = getDepartmentOptions(selectedGroup);
            document.getElementById("div").innerHTML = '<option value="">Select department first</option>';
        }

        function changeOptions2() {
            const deptSelect = document.getElementById("dept");
            const divSelect = document.getElementById("div");
            const selectedDept = deptSelect.value;

            divSelect.innerHTML = getDivisionOptions(selectedDept);
        }

        function showAddModal() {
            const modal = new bootstrap.Modal(document.getElementById('addModal'));
            modal.show();
        }

        function resetAddForm() {
            document.getElementById("addForm").reset();
            document.getElementById("dept").innerHTML = '<option value="">Select a Group first</option>';
            document.getElementById("div").innerHTML = '<option value="">Select a Department first</option>';
        }
    </script>
</head>
<body style="padding: 0%;" onload = "fetchData(); checkRole();">
    <div class="wrapper">
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="lni lni-grid-alt"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="#">INVENTORY</a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="dashboard.php" class="sidebar-link">
                        <img src="Home2.png" width="23" height="23" alt="Home" style="margin-right: 9.5px; margin-left: -3px;">
                        <span>Home</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#auth" aria-expanded="false" aria-controls="auth " id="admintab">
                        <i class="lni lni-user"></i>
                        <span>Admin</span>
                    </a>
                    <ul id="auth" class="sidebar-dropdown list-unstyled collapse">
                        <li class="sidebar-item">
                            <a href="signup_1.php" class="sidebar-link">Create an account</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                    data-bs-target="#multi-two" aria-expanded="false" aria-controls="multi-two">
                        <i class="lni lni-layout"></i>
                        <span>Categories</span>
                    </a>
                    <ul id="multi-two" class="sidebar-dropdown list-unstyled collapse">
                                <li class="sidebar-item">
                                    <a href="hardware.php" class="sidebar-link">Hardware</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="software.php" class="sidebar-link">Software</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="contracts.php" class="sidebar-link">Contracts</a>
                                </li>
                            </ul>
                    
                </li>
                <li class="sidebar-footer">
    <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <i class="lni lni-exit"></i>
        <span>Logout</span>
    </a>
</li>
            </ul>
        </aside>
        <div class="main">
            <div class="text-center" id="main-container">
                <div>
                    <div class="headbox">
                        <h1 style="background-color: #0E733A;" class="header"><img src="https://negrospower.ph/wp-content/uploads/2024/04/Negros-Power-Logo-Black.png" alt="Negros-Power-Logo-Black" style="width: 300px; height: 300px; margin-left: -15px;"onclick=window.location.href="dashboard.php"></h1>
                        <div class="dropdown">
                            <select onchange="location = this.value;" style="background:#0000; width: 150px">
                                <option value="">Contracts</option>
                                <option value="hardware.php">Hardware</option>
                                <option value="software.php">Software</option>
                            </select>
                        </div>
                    </div>

                    <!-- Add Data Modal -->
                    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addModalLabel">Add New Contract</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="resetAddForm()"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="addForm" action="post-contracts-data.php" method="POST" enctype="multipart/form-data">
                                        <table class="form-table">
                                            <tr>
                                                <td><label for="pn">Project Name:</label></td>
                                                <td><input type="text" name="pn" id="pn" required class="wide-input"></td>
                                            </tr>
                                            <tr>
                                                <td><label for="desc">Description:</label></td>
                                                <td><input type="text" name="desc" id="desc" required class="wide-input"></td>
                                            </tr>
                                            <tr>
                                                <td><label for="vendor">Vendor:</label></td>
                                                <td><input type="text" name="vendor" id="vendor" required class="wide-input"></td>
                                            </tr>
                                            <tr>
                                                <td><label for="group">Group:</label></td>
                                                <td>
                                                    <select name="group" id="group" onchange="changeOptions()" required class="wide-input">
                                                        <option value=""></option>
                                                        <option value="CESRA">CESRA</option>
                                                        <option value="NDOG">NDOG</option>
                                                        <option value="Finance">Finance</option>
                                                        <option value="Customer Care">Customer Care</option>
                                                        <option value="Office of the President">Office of the President</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for="dept">Department:</label></td>
                                                <td>
                                                    <select name="department" id="dept" onchange="changeOptions2()" required class="wide-input">
                                                        <option value="">Select a Group first</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for="div">Division:</label></td>
                                                <td>
                                                    <select name="division" id="div" required class="wide-input">
                                                        <option value="">Select a Department first</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for="date">Contract Date:</label></td>
                                                <td><input type="date" name="date" id="date" required class="wide-input"></td>
                                            </tr>
                                            <tr>
                                                <td><label for="enddate">End Date:</label></td>
                                                <td><input type="date" name="enddate" id="enddate" required class="wide-input"></td>
                                            </tr>
                                            <tr>
                                                <td><label for="files">File:</label></td>
                                                <td><input type="file" name="files" id="files" accept=".jpg, .jpeg, .png, .pdf" class="wide-input"></td>
                                            </tr>
                                        </table>
                                        <div class="modal-footer">
                                            <button type="button" class="button" data-bs-dismiss="modal" onclick="resetAddForm()">Cancel</button>
                                            <button type="submit" class="button" name="submit">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin: 20px 0;">
    <input type="text" id="serialNum" placeholder="" class="wide-input" style="width: 300px;" oninput="fetchData()">
    <button class="button" onclick="fetchData()">Search</button>
    <button id="addBtn" class="button" onclick="showAddModal()">Add</button>
    <select id="rowsPerPageSelect" onchange="changeRowsPerPage()" class="wide-input" style="width: 80px;">
        <option value="3">3 rows</option>
        <option value="5">5 rows</option>
        <option value="10">10 rows</option>
    </select>
</div>

                    <table border="0" class="tableClass">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="main-checkbox"></th>
                                <th style="display:none">ID</th>
                                <th>Project Name</th>
                                <th>Description</th>
                                <th>Vendor</th>
                                <th>Group</th>
                                <th>Department</th>
                                <th>Division</th>
                                <th>Contract Date</th>
                                <th>End Date</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody id="dataTable">
                        </tbody>
                    </table>
                    <div id="paginationControls" style="display: none; text-align: center; margin: 10px 0;">
                        <button id="prevBtn" class="button" onclick="goToPrevPage()">Previous</button>
                        <span id="paginationInfo" style="margin: 0 15px;">Page 1 of 1</span>
                        <button id="nextBtn" class="button" onclick="goToNextPage()">Next</button>
                    </div>
                    <div class="button-group">
                        <button id="editBtn" class="button" onclick="toggleEdit()">Edit</button>
                        <button id="updateBtn" class="button" onclick="updateData()" disabled>Update</button>
                        <button id="deleteBtn" class="button" onclick="deleteSelecteds()" disabled>Delete Selected</button>
                        <button class="button" onclick="exportToCSV()">Download .csv file</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" 
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
            crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
    <script src="sidebar.js"></script>
    <script src="disappear.js"></script>
    <!-- Add these modal dialogs right before the closing </body> tag, near your logout modal -->

<!-- Update Confirmation Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Confirm Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to update the selected records?
            </div>
            <div class="modal-footer">
                <button type="button" class="button" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="button" id="confirmUpdateBtn">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the selected records? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="button" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="button" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

    <!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="button" data-bs-dismiss="modal">Cancel</button>
                <a href="logout.php" class="button">Logout</a>
            </div>
        </div>
    </div>
</div>

    <script>
        document.querySelectorAll('.sidebar-link').forEach(link => {
    link.addEventListener('click', function () {
        const sidebar = document.getElementById('sidebar');
        if (!sidebar.classList.contains('expand')) {
            sidebar.classList.add('expand');
        }
    });
});
</script>
</body>
</html>