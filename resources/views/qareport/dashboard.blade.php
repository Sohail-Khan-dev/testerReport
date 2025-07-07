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
              <form method="GET" action="{{ route('dashboard') }}" class="row g-3">
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
                  <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Filter
                  </button>
                  <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Reset
                  </a>
                </div>
              </form>
            </div>
            
            <!-- Charts Container -->
            <div class="row" id="charts-container">
              {{-- @forelse($userData as $userName => $projects) --}}
                @forelse($projects as $projectName => $metrics)
                  <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                      <div class="card-body">
                        <h5 class="card-title">{{ $userName }} - {{ $projectName }}</h5>
                        <div class="chart-container" style="position: relative; height: 300px; width: 300px; margin: 0 auto;">
                          <canvas id="chart-{{ Str::slug($userName) }}-{{ Str::slug($projectName) }}"></canvas>
                        </div>
                      </div>
                    </div>
                  </div>
                {{-- @endforeac-h --}}
              @empty
                <div class="col-12 text-center py-5">
                  <p class="text-muted">No data available for the selected period.</p>
                </div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart.js Script -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Add Chart.js Datalabels plugin -->
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Register the datalabels plugin
      Chart.register(ChartDataLabels);
      
      // Chart colors
      const colors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
        '#FF9F40', '#8AC249', '#EA5545', '#F46A9B', '#EF9B20'
      ];
      
      // Chart data
       // get user from the request of route dashboard.show
       let userData = null;
       function fetchFilteredData() {
          $.ajax({
            url: '/get-dashboared',
            method: 'GET',
            data: {
                from_date: $('#from_date').val() || null, // Get from date if null then get the last 30 days
                to_date: $('#to_date').val() || null, // Get to date if null then get the last 30 days
                user: $('#user').val(),
                project: $('#project').val()
            },
            success: function (response) {
                userData = response.userData;
                renderProjectCumulativeCharts(userData); // Call the new function
            }
          });
        }
      // After fetching userData from AJAX, aggregate by project:
      function renderProjectCumulativeCharts(userData) {
        // 1. Aggregate metrics by project
        const projectTotals = {};
        // Object.entries(userData).forEach(([userName, projects]) => {
          Object.entries(projects).forEach(([projectName, metrics]) => {
            if (!projectTotals[projectName]) {
              projectTotals[projectName] = Array(Object.values(metrics).length).fill(0);
            }
            Object.values(metrics).forEach((val, idx) => {
              projectTotals[projectName][idx] += val;
            });
          });
        // });

        // 2. Clear existing charts
        document.getElementById('charts-container').innerHTML = '';

        // 3. Render one chart per project
        Object.entries(projectTotals).forEach(([projectName, data], i) => {
          const canvasId = `chart-${projectName.toLowerCase().replace(/\s+/g, '-')}`;
          // Create card HTML
          const card = document.createElement('div');
          card.className = 'col-md-6 col-lg-4 mb-4';
          card.innerHTML = `
            <div class="card h-100">
              <div class="card-body">
                <h5 class="card-title">${projectName} (Cumulative)</h5>
                <div class="chart-container" style="position: relative; height: 300px; width: 300px; margin: 0 auto;">
                  <canvas id="${canvasId}"></canvas>
                </div>
              </div>
            </div>
          `;
          document.getElementById('charts-container').appendChild(card);

          const ctx = document.getElementById(canvasId);
          const total = data.reduce((a, b) => a + b, 0);
          if (total === 0) {
            card.remove();
            return;
          }
          new Chart(ctx, {
            type: 'pie',
            data: {
              labels: [
                'Tasks Tested', 'Bugs Reported', 'Regression Testing', 
                'Smoke Testing', 'Client Meeting', 'Daily Meeting',
                'Mobile Testing', 'Automation Testing'
              ],
              datasets: [{
                data: data,
                backgroundColor: colors,
                hoverOffset: 4
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: true,
              plugins: {
                datalabels: {
                  formatter: (value, ctx) => {
                    const percentage = Math.round((value / total) * 100);
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
                      const value = context.raw || 0;
                      const percentage = Math.round((value / total) * 100);
                      return `${label}: ${value} (${percentage}%)`;
                    }
                  }
                }
              }
            }
          });
        });
      }
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

