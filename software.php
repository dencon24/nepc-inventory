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
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
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
                    serial_number: row.querySelector("td:nth-child(3) input").value,
                    an: row.querySelector("td:nth-child(4) input").value,
                    expiry: row.querySelector("td:nth-child(5) input").value,
                    license: row.querySelector("td:nth-child(6) input").value,
                    suppliers: row.querySelector("td:nth-child(7) input").value,
                    receivers: row.querySelector("td:nth-child(8) input").value,
                    software: row.querySelector("td:nth-child(9) input").value,
                    group: row.querySelector(".grp").value,
                    department: row.querySelector(".dept").value,
                    division: row.querySelector(".div").value,
                    position: row.querySelector("td:nth-child(13) input").value,
                    date: row.querySelector("td:nth-child(14) input").value
                };
                data.push(rowData);
            });

            fetch("update-software_data.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                alert(result.message || "Data updated successfully!");
                console.log(result);
                toggleEdit();
                fetchData();
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error updating data" );
            });
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {

            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();

            const allCheckedIds = [];
            document.querySelectorAll("#dataTable input[type='checkbox']:checked").forEach(checkbox => {
                allCheckedIds.push(checkbox.value);
            });

            fetch("delete-software-data.php", {
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

        function checkRole(){
            if(role == "user"){
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

            document.getElementById('main-checkbox').addEventListener('change', function() {
                const isChecked = this.checked;

                allData.forEach(item => {
                    if (isChecked) {
                        checkedIds.add(item.id.toString());
                    } else {
                        checkedIds.delete(item.id.toString());
                    }
                });

                displayPage(currentPage);

                document.getElementById("updateBtn").disabled = !isChecked;
                document.getElementById("deleteBtn").disabled = !isChecked;
            });

            document.addEventListener('change', function(e) {
                if (e.target && e.target.matches('#dataTable input[type="checkbox"]')) {
                    const checkbox = e.target;
                    const id = checkbox.value;

                    if (checkbox.checked) {
                        checkedIds.add(id);
                    } else {
                        checkedIds.delete(id);
                    }

                    updateMainCheckboxState();

                    const anyChecked = checkedIds.size > 0;
                    document.getElementById("updateBtn").disabled = !anyChecked;
                    document.getElementById("deleteBtn").disabled = !anyChecked;
                }
            });
        });

        function exportToCSV() {
            if (checkedIds.size === 0) {
                alert("Please select at least one item to export");
                return;
            }

            const checkedData = allData.filter(item => checkedIds.has(item.id.toString()));

            const headers = [
                "ID",
                "Serial Number",
                "Assignee Name",
                "Expiry",
                "License",
                "Supplier",
                "Receiver",
                "Software",
                "Group",
                "Department",
                "Division",
                "Position",
                "Date"
            ];

            const csvRows = [];
            checkedData.forEach(item => {
                const rowData = [
                    `"${item.id || ''}"`,
                    `"${item.serial_number || ''}"`,
                    `"${item.an || ''}"`,
                    `'${item.expiry || ''}'`,
                    `"${item.license || ''}"`,
                    `"${item.suppliers || ''}"`,
                    `"${item.receivers || ''}"`,
                    `"${item.software || ''}"`,
                    `"${item.group || ''}"`,
                    `"${item.department || ''}"`,
                    `"${item.division || ''}"`,
                    `"${item.position || ''}"`,
                    `'${item.date || ''}'`
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
            link.setAttribute("download", "software_data_" + new Date().toISOString().slice(0,16) + ".csv");
            link.style.visibility = "hidden";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        let editMode = false;

        function toggleEdit() {
            editMode = !editMode;
            const inputs = document.querySelectorAll("#dataTable input, #dataTable select,#dataTable checkbox");
            const fileInputs = document.querySelectorAll("#dataTable input[type='file']");

            inputs.forEach(input => {
                if (input.readOnly !== true ) { 
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
            let serialNum = document.getElementById("serialNum").value;

            let formData = new FormData();
            formData.append("sni", serialNum);

            fetch("get-data_software.php", { 
                method: "POST", 
                body: formData 
            })
            .then(response => response.json())
            .then(data => {
                if (!data || data.length === 0) {
                    document.getElementById("dataTable").innerHTML = 
                        '<tr><td colspan="14">No results found</td></tr>';
                    document.getElementById("paginationControls").style.display = "none";
                    return;
                }

                allData = data;
                currentPage = 1; 
                displayPage(currentPage);
                document.getElementById("paginationControls").style.display = "block";
            })
            .catch(error => {
                console.error("Error:", error);
                document.getElementById("dataTable").innerHTML = 
                    '<tr><td colspan="14">'+error+'</td></tr>';
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
                    <td style="border:none"><input type="text" value="${item.serial_number || ''}" ${editMode ? '' : 'disabled'}></td>
                    <td style="border:none"><input type="text" value="${item.an || ''}" ${editMode ? '' : 'disabled'}></td>
                    <td style="border:none"><input type="date" value="${item.expiry || ''}" ${editMode ? '' : 'disabled'}></td>
                    <td style="border:none"><input type="text" value="${item.license || ''}" ${editMode ? '' : 'disabled'}></td>
                    <td style="border:none; display:none"><input type="text" value="${item.suppliers || ''}" ${editMode ? '' : 'disabled'}></td>
                    <td style="border:none; display:none"><input type="text" value="${item.receivers || ''}" ${editMode ? '' : 'disabled'}></td>
                    <td style="border:none"><input type="text" value="${item.software || ''}" ${editMode ? '' : 'disabled'}></td>
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
                    <td style="border:none; display:none"><input type="text" value="${item.position || ''}" ${editMode ? '' : 'disabled'}></td>
                    <td style="border:none"><input type="date" value="${item.date || ''}" ${editMode ? '' : 'disabled'}></td>
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
        }

        function updateMainCheckboxState() {
            const visibleCheckboxes = document.querySelectorAll("#dataTable input[type='checkbox']");
            const mainCheckbox = document.getElementById("main-checkbox");

            if (visibleCheckboxes.length === 0) {
                mainCheckbox.checked = false;
                return;
            }

            const allChecked = Array.from(visibleCheckboxes).every(checkbox => checkbox.checked);
            mainCheckbox.checked = allChecked;
        }

        function updatePaginationControls() {
            const totalPages = Math.ceil(allData.length / rowsPerPage);
            const paginationInfo = document.getElementById("paginationInfo");
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");

            paginationInfo.textContent = `Page ${currentPage} of ${totalPages}`;
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages || totalPages === 0;

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

        function updateData() {
            const updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
            updateModal.show();
        }

        function deleteSelected() {
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
<body style="padding: 0%;" onload = "fetchData();checkRole();">
    <div class="wrapper">
        <aside id="sidebar" style="font-size: medium;">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="lni lni-grid-alt"></i>
                </button>
                <div class="sidebar-logo">
                    <a href="#" id="test">INVENTORY</a>
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
                    <a class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                        data-bs-target="#auth" aria-expanded="false" aria-controls="auth" id="admintab">
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
                </li>
            </ul>
        </aside>
            <div class="main">
                <div class="text-center" id="main-container">
                <div>
    <div class="headbox">
        <h1 style="background-color: #0E733A ;" class="header"><img src="https://negrospower.ph/wp-content/uploads/2024/04/Negros-Power-Logo-Black.png" alt="Negros-Power-Logo-Black" style="width: 300px; height: 300px; margin-left: -15px;"onclick=window.location.href="dashboard.php"></h1>
        <div class="dropdown">
            <select onchange="location = this.value;" style="background:#0000; width:150px">
                <option value="">Software</option>
                <option value="hardware.php">Hardware</option>
                <option value="contracts.php">Contracts</option>
            </select>
        </div>
    </div>

<!-- Search Section -->
<div style="margin: 20px 0;">
    <input type="text" id="serialNum" oninput="fetchData()" placeholder="" class="wide-input" style="width: 300px;">
    <button class="button" onclick="fetchData()">Search</button>
    <button id="addBtn" class="button" onclick="showAddModal()">Add</button>
</div>

<table border="0" class="tableClass">
    <thead>
        <tr>
            <th><input type="checkbox" id="main-checkbox"></th>
            <!-- Hidden ID column header -->
            <th style="display:none">ID</th>
            <th>Serial Number</th>
            <th>Assignee Name</th>
            <th>Expiry</th>
            <th>License</th>
            <!-- Hidden Supplier column header -->
            <th style="display:none">Supplier</th>
            <!-- Hidden Receiver column header -->
            <th style="display:none">Receiver</th>
            <th>Software</th>
            <th>Group</th>
            <th>Department</th>
            <th>Division</th>
            <!-- Hidden Position column header -->
            <th style="display:none">Position</th>
            <th>Date Issued</th>
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
    <div class = "button-group">
    <button id="editBtn" class="button" onclick="toggleEdit()">Edit</button>
    <button id="updateBtn" class="button" onclick="updateData()" disabled>Update</button>
    <button id="deleteBtn" class="button" onclick="deleteSelected()" disabled>Delete Selected</button>
    <button id="downloadBtn" class="button" onclick="exportToCSV()">Download .csv file</button>
    </div>
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
    <!-- Add Data Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Add New Software Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="resetAddForm()"></button>
            </div>
            <div class="modal-body">
                <form id="addForm" action="post-data-software.php" method="POST" enctype="multipart/form-data">
                    <table class="form-table">
                        <tr>
                            <td><label for="sn">Serial Number:</label></td>
                            <td><input type="text" name="sn" id="sn" required class="wide-input"></td>
                        </tr>
                        <tr>
                            <td><label for="an">Assignee Name:</label></td>
                            <td><input type="text" name="an" id="an" required class="wide-input"></td>
                        </tr>
                        <tr>
                            <td><label for="expiry">Expiry Date:</label></td>
                            <td><input type="date" name="expiry" id="expiry" required class="wide-input"></td>
                        </tr>
                        <tr>
                            <td><label for="license">License:</label></td>
                            <td><input type="text" name="license" id="license" required class="wide-input"></td>
                        </tr>
                        <tr>
                            <td><label for="supplier">Supplier:</label></td>
                            <td><input type="text" name="supplier" id="supplier" required class="wide-input"></td>
                        </tr>
                        <tr>
                            <td><label for="receiver">Receiver:</label></td>
                            <td><input type="text" name="receiver" id="receiver" required class="wide-input"></td>
                        </tr>
                        <tr>
                            <td><label for="software">Software:</label></td>
                            <td><input type="text" name="software" id="software" required class="wide-input"></td>
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
                            <td><label for="position">Position:</label></td>
                            <td><input type="text" name="position" id="position" required class="wide-input"></td>
                        </tr>
                        <tr>
                            <td><label for="date">Date Issued:</label></td>
                            <td><input type="date" name="date" id="date" required class="wide-input"></td>
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