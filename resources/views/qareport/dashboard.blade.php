<x-app-layout>
  <div class="container py-4">
    <div class="row">
      <div class="col-12">
        <div class="card shadow-sm mb-4">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
            <h1 class="card-title h3 mb-4">QA Testing Activity Dashboard</h1>
              <div class="d-flex gap-4 align-items-center">
                <button type="button" class="btn btn-primary" id="send-email-button">
                 <span id="send-email-btn-text">Send Email</span>
                  <span id="send-email-btn-spinner" class="spinner-border spinner-border-sm ms-2" style="display: none;" role="status" aria-hidden="true"></span>
                </button>
                <!-- Show text here when the Email is sent successfully -->   
                <div id="email-sent-message" style="display: none;">
                  Email sent successfully!
                </div>
              </div>
            </div>
            <!-- Date Filter Form -->
            <div class="bg-light p-3 rounded mb-4">
              <form id="filter-form" class="row g-3">
                <div class="col-md-2">
                  <label for="from_date" class="form-label">From Date:</label>
                  <input type="date" id="from_date" name="from_date" class="form-control" 
                    value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                  <label for="to_date" class="form-label">To Date:</label>
                  <input type="date" id="to_date" name="to_date" class="form-control"
                    value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3">
                  <label for="user" class="form-label">User:</label>
                  <select id="user" name="user" class="form-select">
                    <option value="">All Users</option>
                    @foreach($users as $userName => $userId)
                      <option value="{{ $userId }}" {{ request('user') == $userId ? 'selected' : '' }}>{{ $userName }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="project" class="form-label">Project:</label>
                  <select id="project" name="project" class="form-select">
                    <option value="">All Projects</option>
                    @foreach($projects as $projectName => $projectId)
                      <option value="{{ $projectId }}" {{ request('project') == $projectId ? 'selected' : '' }}>{{ $projectName }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                  <button type="button" id="filter-button" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Filter
                  </button>
                  <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Reset
                  </a>
                </div>
              </form>
            </div>
            
            <!-- Bar Chart Container -->
            <div class="row mb-4">
              <div class="col-12">
                <div class="card shadow-sm">
                  <div class="card-body">
                    <h5 class="card-title">Overall Project Totals</h5>
                    <div style="height: 300px;">
                      <canvas id="total-bar-chart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Charts Container -->
            <div class="row" id="charts-container">
              @if($userData->isEmpty())
                <div class="col-12 text-center py-5">
                  <p class="text-muted">No data available for the selected period.</p>
                </div>
              @else
                <!-- Charts will be rendered here by JavaScript -->
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart.js Script -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Add Chart.js Datalabels plugin -->
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Custom plugin to draw text in the center of a doughnut chart
      const centerTextPlugin = {
        id: 'centerText',
        afterDraw: (chart) => {
          if (chart.config.type !== 'doughnut') return;

          const { ctx, chartArea: { top, right, bottom, left, width, height } } = chart;
          // Ensure we're working with numbers and handle any potential null/undefined values
          const data = chart.config.data.datasets[0].data;
          const total = data.reduce((a, b) => {
            const numA = parseInt(a) || 0;
            const numB = parseInt(b) || 0;
            return numA + numB;
          }, 0);

          if (total === 0) return;

          ctx.save();

          // Title
          ctx.font = '1rem sans-serif';
          ctx.textAlign = 'center';
          ctx.textBaseline = 'middle';
          ctx.fillStyle = '#6c757d'; // A neutral color
          const centerX = left + width / 2;
          const centerY = top + height / 2;
          ctx.fillText('Total', centerX, centerY - 15);

          // Value
          ctx.font = 'bold 1.5rem sans-serif';
          ctx.fillStyle = '#333';
          ctx.fillText(total.toString(), centerX, centerY + 15);

          ctx.restore();
        }
      };

      // Register the plugins
      Chart.register(ChartDataLabels, centerTextPlugin);
      
      // Chart colors
      const colors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
        '#FF9F40', '#8AC249', '#EA5545', '#F46A9B', '#EF9B20'
      ];

      let totalBarChartInstance = null;
      let projectChartInstances = {};

      function fetchAndRenderCharts() {
        const from_date = document.getElementById('from_date').value;
        const to_date = document.getElementById('to_date').value;
        const user = document.getElementById('user').value;
        const project = document.getElementById('project').value;

        // Debug logging
        console.log('Filter values:', {
          from_date: from_date,
          to_date: to_date,
          user: user,
          project: project
        });

        document.getElementById('charts-container').innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

        const url = new URL("{{ route('dashboard.data') }}", window.location.origin);
        url.searchParams.set('from_date', from_date);
        url.searchParams.set('to_date', to_date);
        url.searchParams.set('user', user);
        url.searchParams.set('project', project);

        // Add CSRF token for Laravel
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
          axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
        }

        axios.get(url.toString())
          .then(response => {
            console.log('Chart data received:', response.data);
            renderTotalBarChart(response.data.userData);
            renderProjectCumulativeCharts(response.data.userData);
          })
          .catch(error => {
            console.error('Error fetching chart data:', error);
            console.error('Error response:', error.response);
            console.error('Error status:', error.response?.status);
            console.error('Error data:', error.response?.data);

            let errorMessage = 'Failed to load chart data.';

            if (error.response?.status === 401) {
              errorMessage = 'Authentication required. Please log in as an admin to view the dashboard.';
            } else if (error.response?.status === 403) {
              errorMessage = 'Access denied. Admin privileges required to view the dashboard.';
            } else if (error.response?.data?.message) {
              errorMessage += ' ' + error.response.data.message;
            } else if (error.response?.status) {
              errorMessage += ' (Status: ' + error.response.status + ')';
            }

            document.getElementById('charts-container').innerHTML = '<div class="col-12 text-center py-5"><p class="text-danger">' + errorMessage + '</p></div>';
          });
      }

      function renderTotalBarChart(userData) {
        const barChartContainer = document.getElementById('total-bar-chart');
        const barChartCard = barChartContainer.closest('.card');

        if (totalBarChartInstance) {
          totalBarChartInstance.destroy();
        }

        if (!barChartContainer || !userData || userData.length === 0) {
          // If no data, clear the bar chart area or show a message
          if (barChartCard) barChartCard.style.display = 'none';
          return;
        }
        if (barChartCard) barChartCard.style.display = 'block';

        const labels = ['Tasks Tested', 'Bugs Reported', 'Daily Meetings'];
        const totals = {
          tasks_tested: 0,
          bugs_reported: 0,
          daily_meeting: 0
        };

        userData.forEach(project => {
          totals.tasks_tested += parseInt(project.tasks_tested) || 0;
          totals.bugs_reported += parseInt(project.bugs_reported) || 0;
          totals.daily_meeting += parseInt(project.daily_meeting) || 0;
        });

        const data = [
          totals.tasks_tested,
          totals.bugs_reported,
          totals.daily_meeting
        ];

        totalBarChartInstance = new Chart(barChartContainer, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'Total Counts',
              data: data,
              backgroundColor: [
                'rgba(54, 162, 235, 0.5)',
                'rgba(255, 99, 132, 0.5)',
                'rgba(255, 206, 86, 0.5)'
              ],
              borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 206, 86, 1)'
              ],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              },
              title: {
                display: false,
              },
              datalabels: {
                anchor: 'end',
                align: 'top',
                formatter: (value) => value > 0 ? value : '',
                color: '#444'
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
              }
            }
          }
        });
      }

      function renderProjectCumulativeCharts(userData) {
        const container = document.getElementById('charts-container');
        container.innerHTML = ''; // Clear existing charts

        // Destroy previous chart instances to prevent memory leaks
        Object.values(projectChartInstances).forEach(chart => chart.destroy());
        projectChartInstances = {};

        if (userData.length === 0) {
          container.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted">No data available for the selected filters.</p></div>';
          return;
        }

        // Render one chart per project from the pre-aggregated data
        userData.forEach(project => {
          const canvasId = `chart-${project.project_name.toLowerCase().replace(/\s+/g, '-')}`;
          // Create card HTML
          const card = document.createElement('div');
          card.className = 'col-md-6 col-lg-4 mb-4';
          card.innerHTML = `
            <div class="card h-100">
              <div class="card-body">
                <h5 class="card-title">${project.project_name}</h5>
                <div class="chart-container" style="position: relative; height: 300px; width: 300px; margin: 0 auto;">
                  <canvas id="${canvasId}"></canvas>
                </div>
              </div>
            </div>
          `;
          container.appendChild(card);

          const ctx = document.getElementById(canvasId);
          // Ensure all values are properly converted to integers
          const data = [
            parseInt(project.tasks_tested) || 0,
            parseInt(project.bugs_reported) || 0,
            parseInt(project.daily_meeting) || 0
            // Add other metrics from the response here
          ];
          const total = data.reduce((a, b) => a + b, 0);

          if (total === 0) {
            card.remove();
            return;
          }

          const chart = new Chart(ctx, {
            type: 'doughnut', // Changed from pie to doughnut
            data: {
              labels: ['Tasks Tested', 'Bugs Reported', 'Daily Meetings'],
              datasets: [{
                data: data,
                backgroundColor: colors,
                hoverOffset: 4
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              cutout: '60%', // Adjust doughnut thickness
              plugins: {
                centerText: {}, // Enable our custom plugin
                datalabels: {
                  formatter: (value, ctx) => {
                    const numValue = parseInt(value) || 0;
                    const percentage = Math.round((numValue / total) * 100);
                    if (percentage < 5) return '';
                    return percentage + '%';
                  },
                  color: '#fff',
                  font: {
                    weight: 'bold',
                    size: 10
                  }
                },
                legend: {
                  position: 'bottom',
                  labels: {
                    boxWidth: 8,
                    font: { size: 10 }
                  }
                },
                tooltip: {
                  callbacks: {
                    label: function(context) {
                      const label = context.label || '';
                      const value = parseInt(context.raw) || 0;
                      const percentage = Math.round((value / total) * 100);
                      return `${label}: ${value} (${percentage}%)`;
                    }
                  }
                }
              }
            }
          });

          projectChartInstances[canvasId] = chart;
        });
      }

      // Event Listeners
      document.getElementById('filter-button').addEventListener('click', function(e) {
        e.preventDefault();
        fetchAndRenderCharts();
      });

      // Initial data load
      fetchAndRenderCharts();

      // Send Email Button Click Handler
      document.getElementById('send-email-button').addEventListener('click', function() {
        document.getElementById('send-email-button').disabled = true;
        const btnSpinner = document.getElementById('send-email-btn-spinner');
        btnSpinner.style.display = 'inline-block';
        
        axios.post('/send-daily-notifications')
            .then(function(response) {
              console.log(response.data);
              btnSpinner.style.display = 'none';
              document.getElementById('email-sent-message').style.display = 'block';
              Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: response.data.message,
                timer: 3000,
                showConfirmButton: false
              });
            })
            .catch(function(error) {
              let message = 'An error occurred while sending the email.';
              document.getElementById('email-sent-message').style.display = 'none';
              document.getElementById("email-sent-message").textContent = message;
              console.error(error);
            });
        });
    });
  </script>
</x-app-layout>
