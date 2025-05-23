/**
 * Tester Report - Theme JavaScript
 * Handles theme functionality like sidebar toggle, dropdowns, etc.
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize theme functionality
  initTheme();
  
  /**
   * Initialize all theme functionality
   */
  function initTheme() {
    // Initialize sidebar
    initSidebar();
    
    // Initialize dropdowns
    initDropdowns();
    
    // Initialize tooltips
    initTooltips();
    
    // Initialize modals
    initModals();
    
    // Initialize DataTables
    initDataTables();
  }
  
  /**
   * Initialize sidebar functionality
   */
  function initSidebar() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const navbarToggle = document.querySelector('.navbar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
    
    // Toggle sidebar on sidebar toggle button click
    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('sidebar-collapsed');
        
        // Save sidebar state to localStorage
        const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
        localStorage.setItem('sidebar-collapsed', isCollapsed);
      });
    }
    
    // Toggle sidebar on navbar toggle button click (mobile)
    if (navbarToggle) {
      navbarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('show');
        
        if (sidebarBackdrop) {
          sidebarBackdrop.classList.toggle('show');
        }
      });
    }
    
    // Hide sidebar when clicking on backdrop (mobile)
    if (sidebarBackdrop) {
      sidebarBackdrop.addEventListener('click', function() {
        sidebar.classList.remove('show');
        sidebarBackdrop.classList.remove('show');
      });
    }
    
    // Set active sidebar item based on current URL
    const currentPath = window.location.pathname;
    const sidebarLinks = document.querySelectorAll('.sidebar-nav-link');
    
    sidebarLinks.forEach(link => {
      const href = link.getAttribute('href');
      
      if (href === currentPath || currentPath.startsWith(href)) {
        link.classList.add('active');
      }
    });
    
    // Restore sidebar state from localStorage
    const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
    
    if (isCollapsed) {
      sidebar.classList.add('sidebar-collapsed');
    }
  }
  
  /**
   * Initialize dropdown functionality
   */
  function initDropdowns() {
    const dropdownToggles = document.querySelectorAll('[data-toggle="dropdown"]');
    
    dropdownToggles.forEach(toggle => {
      toggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const dropdown = this.closest('.navbar-dropdown');
        
        // Close all other dropdowns
        document.querySelectorAll('.navbar-dropdown.show').forEach(el => {
          if (el !== dropdown) {
            el.classList.remove('show');
          }
        });
        
        // Toggle current dropdown
        dropdown.classList.toggle('show');
      });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.navbar-dropdown')) {
        document.querySelectorAll('.navbar-dropdown.show').forEach(el => {
          el.classList.remove('show');
        });
      }
    });
  }
  
  /**
   * Initialize tooltip functionality
   */
  function initTooltips() {
    const tooltipTriggers = document.querySelectorAll('[data-toggle="tooltip"]');
    
    tooltipTriggers.forEach(trigger => {
      const title = trigger.getAttribute('title') || trigger.getAttribute('data-title');
      const placement = trigger.getAttribute('data-placement') || 'top';
      
      if (title) {
        // Create tooltip element
        const tooltip = document.createElement('div');
        tooltip.classList.add('tooltip');
        tooltip.classList.add(`tooltip-${placement}`);
        tooltip.innerHTML = `<div class="tooltip-arrow"></div><div class="tooltip-inner">${title}</div>`;
        document.body.appendChild(tooltip);
        
        // Show tooltip on hover
        trigger.addEventListener('mouseenter', function() {
          const rect = trigger.getBoundingClientRect();
          
          // Position tooltip based on placement
          if (placement === 'top') {
            tooltip.style.left = `${rect.left + rect.width / 2}px`;
            tooltip.style.top = `${rect.top - 10}px`;
          } else if (placement === 'bottom') {
            tooltip.style.left = `${rect.left + rect.width / 2}px`;
            tooltip.style.top = `${rect.bottom + 10}px`;
          } else if (placement === 'left') {
            tooltip.style.left = `${rect.left - 10}px`;
            tooltip.style.top = `${rect.top + rect.height / 2}px`;
          } else if (placement === 'right') {
            tooltip.style.left = `${rect.right + 10}px`;
            tooltip.style.top = `${rect.top + rect.height / 2}px`;
          }
          
          tooltip.classList.add('show');
        });
        
        // Hide tooltip on mouse leave
        trigger.addEventListener('mouseleave', function() {
          tooltip.classList.remove('show');
        });
      }
    });
  }
  
  /**
   * Initialize modal functionality
   */
  function initModals() {
    const modalTriggers = document.querySelectorAll('[data-toggle="modal"]');
    
    modalTriggers.forEach(trigger => {
      trigger.addEventListener('click', function(e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('data-target');
        const modal = document.querySelector(targetId);
        
        if (modal) {
          modal.classList.add('show');
          document.body.classList.add('modal-open');
        }
      });
    });
    
    // Close modal when clicking on close button
    document.querySelectorAll('.modal-close').forEach(button => {
      button.addEventListener('click', function() {
        const modal = this.closest('.modal');
        
        if (modal) {
          modal.classList.remove('show');
          document.body.classList.remove('modal-open');
        }
      });
    });
    
    // Close modal when clicking on backdrop
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
      backdrop.addEventListener('click', function() {
        const modal = this.closest('.modal');
        
        if (modal) {
          modal.classList.remove('show');
          document.body.classList.remove('modal-open');
        }
      });
    });
  }
  
  /**
   * Initialize DataTables with custom styling
   */
  function initDataTables() {
    if ($.fn.DataTable) {
      $.extend(true, $.fn.DataTable.defaults, {
        language: {
          paginate: {
            previous: '<i class="fas fa-chevron-left"></i>',
            next: '<i class="fas fa-chevron-right"></i>'
          },
          search:  '' , //'<i class="fas fa-search"></i> _INPUT_',
          searchPlaceholder: 'Search...',
          lengthMenu: 'Show _MENU_ entries',
          info: 'Showing _START_ to _END_ of _TOTAL_ entries',
          infoEmpty: 'Showing 0 to 0 of 0 entries',
          infoFiltered: '(filtered from _MAX_ total entries)',
          zeroRecords: 'No matching records found',
          emptyTable: 'No data available in table'
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        responsive: true,
        autoWidth: false
      });
    }
  }
});
