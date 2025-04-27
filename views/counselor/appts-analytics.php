<?php 
    session_start();

    include(__DIR__ . "/../../config/utils.php");
    
    // check session first exists first
    if (!isset($_SESSION['counselorId']) || !isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'Counselor') {
        header("location: ../public/counselor-admin-login-page.php");
        exit();
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/counselor.css">
    <title>Analytics - Appointment </title>
    <style>

        .appt-header-bar {
            width: 100%;
            background-color:rgb(222, 237, 251);
            border-bottom: 2px #9DCEFF solid;
        }

        .appt-header-bar h5{
            color: var(--primary-color);
        }

        .total-analytics-card {
            width: 100%;
            border-radius: 8px;
        }
        
        .analytics-num {
            color: var(--primary-color);
        }

    </style>
</head>
<body>
    <?php 
        include(__DIR__ . '/../components/counselor/sidebar.php');
    ?>
    <main>
        <div class="appt-header-bar px-4 py-3 d-flex align-items-center mb-4">
            <h5 class="mb-0 fw-bold">Appointments</h5>
        </div>
        <div class="px-4 w-100 d-flex gap-3 mb-5">
            <div class="total-analytics-card shadow px-3 py-2 bg-body-tertiary d-flex flex-column justify-content-between gap-2">
                <div class="d-flex align-items-center justify-content-between">
                    <p class="fw-bold mb-0 analytics-num fs-1">100</p>
                    <i class="bi bi-calendar-check-fill fs-3" style="color: var(--primary-color)"></i>
                </div>
                <p style="font-size: 14px;">Total Number of Appointments Completed This Year</p>
            </div>
            <div class="total-analytics-card shadow px-3 py-2 bg-body-tertiary d-flex flex-column justify-content-between gap-2">
                <div class="d-flex align-items-center justify-content-between">
                    <p class="fw-bold mb-0 analytics-num fs-1">100</p>
                    <i class="bi bi-bar-chart-line-fill fs-3" style="color: var(--primary-color)"></i>
                </div>
                <div>
                    <p style="font-size: 14px;" class="mb-0">Average Number of Appointment Requests Per Month</p>
                    <p class="fst-italic" style="font-size: 11px;">*Based on the number of appointments requested this year.</p>
                </div>
            </div>
            <div class="total-analytics-card shadow px-3 py-2 bg-body-tertiary d-flex flex-column justify-content-between gap-2">
                <div class="d-flex align-items-center justify-content-between">
                    <p class="fw-bold mb-0 analytics-num fs-1">100</p>
                    <!-- <i class="bi bi-arrow-down-right fs-3" style="color: var(--primary-color)"></i> -->
                    <!-- <i class="bi bi-arrow-up-right fs-3" style="color: var(--primary-color)"></i> -->
                </div>
                <p style="font-size: 14px;">in number of appointments requested this year compared to last year's total.</p>
            </div>
        </div>
        <h5 class="fw-bold px-4">Appointment Frequency and Trends</h5>
        <p class="px-4" style="font-size: 14px;">Below is an overview of monthly completed appointments and a breakdown of counseling concerns and their percentage distribution.</p>
        <div class="px-4 d-flex gap-3 mb-5 align-items-stretch">
            <div class="w-75 bg-body-tertiary shadow p-2 rounded">
                <canvas id="lineChart"></canvas>
            </div>
            <div class="w-25 bg-body-tertiary shadow d-flex p-2 rounded align-items-center">
                <canvas class="" id="pieChart"></canvas>  
            </div>
        </div>
        <h5 class="fw-bold px-4">Student Demographics</h5>
        <p class="px-4" style="font-size: 14px;">Below is a breakdown of student demographics for counseling appointments. Data was based on the number of students who requested appointments this year.</p>
        <div class="px-4 d-flex gap-3 mb-4 align-items-stretch">
            <div class="w-25 bg-body-tertiary shadow d-flex p-2 rounded align-items-center">
                <canvas id="genderChart"></canvas>
            </div>
            <div class="w-75 bg-body-tertiary  shadow p-2 rounded">
                <canvas id="programBarChart"></canvas>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const getCurrentYear = () => {
            const date = new Date();
            return date.getFullYear();
        };

        const oneYearOffset = 1; // Offset for the previous year

        const ctx = document.getElementById('lineChart').getContext('2d');

        const data = {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'November', 'December'],
            datasets: [{
                label: `Appointments Completed Last Year (${getCurrentYear() - oneYearOffset})`,
                data: [10, 20, 15, 25, 30, 35, 40, 90, 67, 10, 11, 89],
                borderColor: 'rgb(180, 75, 192)',
                backgroundColor: 'rgba(192, 75, 186, 0.2)', 
                borderWidth: 2,
                fill: true, 
                tension: 0.4 
            },
            {
                label: `Appointments Completed This Year (${getCurrentYear()})`,
                data: [20, 10, 8, 12, 18, 4, 5],
                borderColor: 'rgb(75, 126, 192)',
                backgroundColor: 'rgba(75, 106, 192, 0.2)', 
                borderWidth: 2,
                fill: true, 
                tension: 0.4 
            }]
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    title: {
                        display: true, 
                        text: 'Total Number of Appointments Completed Each Month In a Year', 
                    },
                    subtitle: {
                        display: true,
                        text: `A comparison of last year's and this year's completed number of appointments each month.`,
                        font: { style: 'italic'},
                        padding: {
                            bottom: 20 
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Months'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Appointments'
                        },
                        beginAtZero: true
                    }
                }
            }
        };

        const lineChart = new Chart(ctx, config);

        const pieCtx = document.getElementById('pieChart').getContext('2d');

        const pieData = {
            labels: ['Personal', 'Academic', 'Career'],
            datasets: [{
                label: 'Appointment Categories',
                data: [50, 30, 20], 
                backgroundColor: [
                    'rgba(99, 255, 198, 0.6)', 
                    'rgba(184, 54, 235, 0.6)', 
                    'rgba(255, 168, 86, 0.6)'  
                ],
                borderColor: [
                    'rgba(99, 255, 198, 1)', 
                    'rgba(184, 54, 235, 1)', 
                    'rgba(255, 168, 86, 1)'  
                ],
                borderWidth: 1
            }],
        };

        const pieConfig = {
            type: 'pie',
            data: pieData,
            options: {
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    title: {
                        display: true, 
                        text: 'Percentage of Counseling Concerns', 
                    },
                    subtitle: {
                        display: true,
                        text: [`*Based on the number of `, `appointment requests this ${getCurrentYear()}`],
                        font: { style: 'italic'},
                        padding: {
                            bottom: 20 
                        }
                    }
                }
            }
        };

        const pieChart = new Chart(pieCtx, pieConfig);
        const genderCtx = document.getElementById('genderChart').getContext('2d');

        const genderData = {
            labels: ['Male', 'Female'], 
            datasets: [{
                label: 'Gender Demographics',
                data: [60, 35], 
                backgroundColor: [
                    'rgba(54, 162, 235, 0.6)', 
                    'rgba(255, 99, 132, 0.6)', 
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        };

        const genderConfig = {
            type: 'doughnut', 
            data: genderData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    title: {
                            display: true, 
                            text: 'Percentage of Appointments by Gender', 
                        },
                    subtitle: {
                        display: true,
                        text: [`*Based on the number of `, `appointment requests this ${getCurrentYear()}`],
                        font: { style: 'italic'},
                        padding: {
                            bottom: 20
                        }
                    }
                }
            }
        };

        const genderChart = new Chart(genderCtx, genderConfig);
        const programCtx = document.getElementById('programBarChart').getContext('2d');

        const programLabels = [
            'BSA', 'BSMA', 'BSIT', 'BSENT', 'BSEcE', 
            'BSIE', 'BECED', 'BSIS', 'BSCS', 'BSCpE'
        ];
        const programData = [15, 10, 25, 5, 8, 12, 7, 20, 18, 10]; 

        const programBarData = {
            labels: programLabels,
            datasets: [{
                label: 'Number of Appointments',
                data: programData,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };

        const programBarConfig = {
            type: 'bar',
            data: programBarData,
            options: {
                responsive: true,
                indexAxis: 'y', 
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true, 
                        text:'Distribution of Appointments Across Programs',
                    },
                    subtitle: {
                        display: true,
                        text: `*Based on the number of appointment requests this ${getCurrentYear()}`,
                        font: {
                            style: 'italic'
                        },
                        padding: {
                            bottom: 20 
                        }
                    }
                },
                
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Number of Appointments'
                        },
                        beginAtZero: true
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Programs'
                        }
                    }
                }
            }
        };

        const programBarChart = new Chart(programCtx, programBarConfig);
    </script>
</body>
</html>
