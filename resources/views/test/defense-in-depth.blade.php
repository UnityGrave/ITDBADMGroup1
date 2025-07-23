<x-app-layout>
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-50 to-blue-50 rounded-lg shadow-md p-6 mb-6">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">🛡️ Defense-in-Depth Security System</h1>
                <p class="text-gray-600 text-lg">Laravel Application + MySQL Database Layer RBAC Integration</p>
                <p class="text-sm text-gray-500 mt-2">Live demonstration of Gatekeeper (Application Layer) + Vault (Database Layer) security</p>
            </div>
        </div>

        <!-- Security Architecture Overview -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">🏗️ Security Architecture</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="flex items-center mb-3">
                        <span class="text-2xl mr-3">👮</span>
                        <h3 class="text-xl font-semibold text-blue-800">GATEKEEPER (Laravel)</h3>
                    </div>
                    <p class="text-blue-700 text-sm mb-3">Application-level authorization that checks user roles and permissions before allowing any operation.</p>
                    <ul class="text-xs text-blue-600 space-y-1">
                        <li>• Authenticates users and validates roles</li>
                        <li>• Controls UI access (buttons, forms, pages)</li>
                        <li>• Enforces business logic rules</li>
                        <li>• First line of defense</li>
                    </ul>
                </div>

                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="flex items-center mb-3">
                        <span class="text-2xl mr-3">🔒</span>
                        <h3 class="text-xl font-semibold text-green-800">VAULT (MySQL)</h3>
                    </div>
                    <p class="text-green-700 text-sm mb-3">Database-level security that uses different MySQL users with specific GRANT/REVOKE privileges.</p>
                    <ul class="text-xs text-green-600 space-y-1">
                        <li>• Separate database users per operation type</li>
                        <li>• MySQL enforces privilege restrictions</li>
                        <li>• Principle of Least Privilege</li>
                        <li>• Final security barrier</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Current User Status -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">👤 Your Security Profile</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-700 mb-2">User Information</h4>
                    <p class="text-sm"><strong>Name:</strong> {{ $user->name }}</p>
                    <p class="text-sm"><strong>Email:</strong> {{ $user->email }}</p>
                    <p class="text-sm"><strong>ID:</strong> {{ $user->id }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-700 mb-2">Assigned Roles</h4>
                    <div class="space-y-1">
                        @foreach($userRoles as $role)
                            @if($role === 'Admin')
                                <span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded">
                                    👑 {{ $role }}
                                </span>
                            @elseif($role === 'Employee')
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                    👨‍💼 {{ $role }}
                                </span>
                            @else
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                    🛍️ {{ $role }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-700 mb-2">Available Operations</h4>
                    <div class="space-y-1">
                        @foreach($availableOperations as $operation)
                            <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">
                                {{ $operation }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Operation Types and Tests -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">🧪 Live Security Tests</h2>
            <p class="text-gray-600 mb-6">Click the buttons below to test different operation types. Each test demonstrates both GATEKEEPER and VAULT security layers.</p>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @foreach($operationDescriptions as $operation => $info)
                    <div class="border rounded-lg p-4 {{ in_array($operation, $availableOperations) ? 'border-green-300 bg-green-50' : 'border-red-300 bg-red-50' }}">
                        <div class="text-center">
                            <h3 class="font-semibold text-gray-800 mb-2">{{ $operation }}</h3>
                            <p class="text-xs text-gray-600 mb-3">{{ $info['description'] }}</p>
                            <p class="text-xs font-mono bg-gray-100 p-2 rounded mb-3">{{ $info['connection'] }}</p>
                            <p class="text-xs text-gray-500 mb-3">{{ $info['privileges'] }}</p>
                            
                            @if(in_array($operation, $availableOperations))
                                <button 
                                    onclick="testOperation('{{ strtolower(str_replace('_', '-', $operation)) }}')"
                                    class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-2 rounded transition-colors">
                                    ✅ Test {{ $operation }}
                                </button>
                            @else
                                <button 
                                    onclick="testUnauthorizedOperation('{{ $operation }}')"
                                    class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-2 rounded transition-colors">
                                    🚫 Test Blocked
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Additional test buttons -->
            <div class="border-t pt-4">
                <div class="grid md:grid-cols-3 gap-4">
                    <button 
                        onclick="testUnauthorizedAccess()" 
                        class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded transition-colors">
                        🚨 Test Unauthorized Access
                    </button>
                    
                    <button 
                        onclick="getOperationStatus()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors">
                        📊 Get System Status
                    </button>

                    <button 
                        onclick="clearResults()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors">
                        🗑️ Clear Results
                    </button>
                </div>
            </div>
        </div>

        <!-- Test Results Display -->
        <div id="test-results" class="bg-white rounded-lg shadow-md p-6 mb-6" style="display: none;">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">📋 Test Results</h2>
            <div id="results-content"></div>
        </div>

        <!-- Security Benefits -->
        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">💡 Defense-in-Depth Benefits</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-green-800 mb-3">🛡️ Enhanced Security</h3>
                    <ul class="text-sm text-green-700 space-y-2">
                        <li>• <strong>Double Protection:</strong> Application + Database layers</li>
                        <li>• <strong>Principle of Least Privilege:</strong> Minimum required permissions</li>
                        <li>• <strong>Breach Containment:</strong> Even if app is compromised, database is protected</li>
                        <li>• <strong>Granular Control:</strong> Different operations use different privileges</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-blue-800 mb-3">⚡ Operational Benefits</h3>
                    <ul class="text-sm text-blue-700 space-y-2">
                        <li>• <strong>Audit Trail:</strong> Database logs show which user performed what</li>
                        <li>• <strong>Compliance:</strong> Meets enterprise security requirements</li>
                        <li>• <strong>Performance:</strong> Optimized connections per operation type</li>
                        <li>• <strong>Maintainability:</strong> Clear separation of concerns</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Back to Dashboard -->
        <div class="text-center">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-lg">
                ← Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- CSRF Token for AJAX requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
// Set up CSRF token for AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function testOperation(operationType) {
    showLoading(`Testing ${operationType.toUpperCase()} operation...`);
    
    let url = '';
    let data = {};
    
    switch(operationType) {
        case 'read':
            url = '{{ route("test.defense.read") }}';
            break;
        case 'data-entry':
            url = '{{ route("test.defense.data-entry") }}';
            data = { test_name: 'Defense-in-Depth Test' };
            break;
        case 'admin-ops':
            url = '{{ route("test.defense.admin-ops") }}';
            data = { user_id: '{{ $user->id }}' };
            break;
        case 'system-admin':
            url = '{{ route("test.defense.system-admin") }}';
            break;
    }
    
    $.post(url, data)
        .done(function(response) {
            displayResult(response, 'success');
        })
        .fail(function(xhr) {
            const response = xhr.responseJSON || { error: 'Unknown error occurred' };
            displayResult(response, 'error');
        });
}

function testUnauthorizedOperation(operationType) {
    showLoading(`Testing unauthorized access to ${operationType}...`);
    
    $.post('{{ route("test.defense.unauthorized") }}')
        .done(function(response) {
            displayResult(response, response.success ? 'success' : 'error');
        })
        .fail(function(xhr) {
            const response = xhr.responseJSON || { error: 'Unknown error occurred' };
            displayResult(response, 'error');
        });
}

function testUnauthorizedAccess() {
    testUnauthorizedOperation('SYSTEM_ADMIN');
}

function getOperationStatus() {
    showLoading('Getting system status...');
    
    $.get('{{ route("test.defense.status") }}')
        .done(function(response) {
            displayResult(response, 'info');
        })
        .fail(function(xhr) {
            const response = xhr.responseJSON || { error: 'Unknown error occurred' };
            displayResult(response, 'error');
        });
}

function showLoading(message) {
    const resultsDiv = document.getElementById('test-results');
    const contentDiv = document.getElementById('results-content');
    
    contentDiv.innerHTML = `
        <div class="flex items-center justify-center p-6">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
            <span class="text-gray-700">${message}</span>
        </div>
    `;
    
    resultsDiv.style.display = 'block';
    resultsDiv.scrollIntoView({ behavior: 'smooth' });
}

function displayResult(response, type) {
    const resultsDiv = document.getElementById('test-results');
    const contentDiv = document.getElementById('results-content');
    
    let alertClass = '';
    let icon = '';
    
    switch(type) {
        case 'success':
            alertClass = 'bg-green-50 border-green-200 text-green-800';
            icon = '✅';
            break;
        case 'error':
            alertClass = 'bg-red-50 border-red-200 text-red-800';
            icon = '❌';
            break;
        case 'info':
            alertClass = 'bg-blue-50 border-blue-200 text-blue-800';
            icon = 'ℹ️';
            break;
    }
    
    let resultHtml = `
        <div class="${alertClass} border rounded-lg p-4 mb-4">
            <div class="flex items-start">
                <span class="text-2xl mr-3">${icon}</span>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold mb-2">${response.message || 'Operation Result'}</h3>
                    
                    ${response.gatekeeper_check ? `
                        <div class="mb-2">
                            <strong>🏛️ GATEKEEPER Status:</strong> ${response.gatekeeper_check}
                        </div>
                    ` : ''}
                    
                    ${response.vault_connection ? `
                        <div class="mb-2">
                            <strong>🔒 VAULT Connection:</strong> ${response.vault_connection}
                        </div>
                    ` : ''}
                    
                    ${response.gatekeeper_status ? `
                        <div class="mb-2">
                            <strong>🚨 Security Status:</strong> ${response.gatekeeper_status}
                            ${response.blocked_reason ? `<br><em>Reason: ${response.blocked_reason}</em>` : ''}
                        </div>
                    ` : ''}
                    
                    ${response.warning ? `
                        <div class="mb-2 text-orange-600">
                            <strong>⚠️ Warning:</strong> ${response.warning}
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
    
    if (response.result || response.user_info || response.error) {
        resultHtml += `
            <div class="bg-gray-50 rounded-lg p-4 mt-4">
                <h4 class="font-semibold text-gray-800 mb-2">🔍 Detailed Results:</h4>
                <pre class="text-xs bg-white p-3 rounded border overflow-auto">${JSON.stringify(response.result || response.user_info || { error: response.error }, null, 2)}</pre>
            </div>
        `;
    }
    
    contentDiv.innerHTML = resultHtml;
    resultsDiv.style.display = 'block';
    resultsDiv.scrollIntoView({ behavior: 'smooth' });
}

function clearResults() {
    const resultsDiv = document.getElementById('test-results');
    resultsDiv.style.display = 'none';
}
</script>

</x-app-layout>