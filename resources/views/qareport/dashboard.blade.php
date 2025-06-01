<x-app-layout>
  <div class="container py-4">
    <div class="row">
      <div class="col-12">
        <div class="card shadow-sm mb-4">
          <div class="card-body">
            <h1 class="card-title h3 mb-4">QA Testing Activity Dashboard</h1>
            
            <!-- Date Filter Form -->
            <div class="bg-light p-3 rounded mb-4">
              <form method="GET" action="{{ route('dashboard') }}" class="row g-3">
                <div class="col-md-3">
                  <label for="from_date" class="form-label">From Date:</label>
                  <input type="date" id="from_date" name="from_date" class="form-control" 
                    value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3">
                  <label for="to_date" class="form-label">To Date:</label>
                  <input type="date" id="to_date" name="to_date" class="form-control"
                    value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
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
              @forelse($userData as $userName => $projects)
                @foreach($projects as $projectName => $metrics)
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
                @endforeach
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
      const userData = @json($userData);
      
      // Create charts for each user and project
      Object.entries(userData).forEach(([userName, projects]) => {
        Object.entries(projects).forEach(([projectName, metrics]) => {
          const canvasId = `chart-${userName.toLowerCase().replace(/\s+/g, '-')}-${projectName.toLowerCase().replace(/\s+/g, '-')}`;
          const ctx = document.getElementById(canvasId);
          
          if (ctx) {
            const data = Object.values(metrics);
            const total = data.reduce((a, b) => a + b, 0);
            
            // Skip rendering if there's no data
            if (total === 0) {
              // Find the parent container and remove it from the DOM
              const container = ctx.closest('.col-md-6');
              if (container) {
                container.remove();
              }
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
                  // Configure the datalabels plugin
                  datalabels: {
                    formatter: (value, ctx) => {
                      // Only show percentage for segments that are large enough
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
                      font: {
                        size: 10
                      }
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
          }
        });
      });
    });
  </script>
</x-app-layout>

