<x-app-layout>
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-4xl mx-auto px-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800">🛡️ {{ $title }}</h1>
                <p class="text-gray-600 mt-2">Authorization System Test</p>
            </div>
        </div>

        <!-- Success Message -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ✅
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-green-800">Access Granted!</h3>
                    <p class="text-green-700 mt-1">{{ $message }}</p>
                </div>
            </div>
        </div>

        <!-- User Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">👤 Current User Information</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <p class="text-gray-900">{{ auth()->user()->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="text-gray-900">{{ auth()->user()->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">User ID</label>
                    <p class="text-gray-900">{{ auth()->user()->id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Assigned Roles</label>
                    <div class="flex flex-wrap gap-1 mt-1">
                        @foreach($user_roles as $role)
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
            </div>
        </div>

        <!-- Synchronized Database Connection Information -->
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-blue-800 mb-4">🔗 Synchronized Database-Level RBAC</h2>
            <div class="space-y-4">
                <div class="bg-white rounded-lg p-4 border border-blue-100">
                    <label class="block text-sm font-medium text-blue-700 mb-2">🔄 Synchronized Credentials</label>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Laravel Login:</span>
                            <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ auth()->user()->email }}</span>
                            <span class="text-xs text-gray-500">password: <code>password</code></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">MySQL User:</span>
                            <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">
                                @if(auth()->user()->email === 'admin@konibui.com')
                                    admin_konibui
                                @elseif(auth()->user()->email === 'employee@konibui.com')
                                    employee_konibui
                                @elseif(auth()->user()->email === 'test@example.com')
                                    customer_test
                                @else
                                    <span class="text-orange-600">NOT MAPPED</span>
                                @endif
                            </span>
                            <span class="text-xs text-gray-500">password: <code>password</code></span>
                        </div>
                        @if(in_array(auth()->user()->email, ['admin@konibui.com', 'employee@konibui.com', 'test@example.com']))
                            <div class="flex items-center space-x-2 text-green-700">
                                <span class="text-xs">✅</span>
                                <span class="text-xs font-medium">SAME CREDENTIALS for both Laravel and MySQL!</span>
                            </div>
                        @else
                            <div class="flex items-center space-x-2 text-orange-600">
                                <span class="text-xs">⚠️</span>
                                <span class="text-xs">This account only has Laravel access (no MySQL mapping)</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-4 border border-blue-100">
                    <label class="block text-sm font-medium text-blue-700 mb-2">🔗 Active Database Connection</label>
                    <p class="text-blue-900 font-mono text-sm mb-2">
                        @if(in_array('Admin', $user_roles))
                            mysql_admin <span class="text-green-600">(Full Database Privileges)</span>
                        @elseif(in_array('Employee', $user_roles))
                            mysql_staff <span class="text-yellow-600">(Limited Database Privileges)</span>
                        @else
                            mysql_customer <span class="text-blue-600">(Restricted Database Privileges)</span>
                        @endif
                    </p>
                    
                    <div class="text-sm text-blue-800">
                        @if(in_array('Admin', $user_roles))
                            <p>🔴 <strong>admin_konibui:</strong> GRANT ALL PRIVILEGES ON konibui.*</p>
                            <p class="text-xs text-blue-600">→ Can SELECT, INSERT, UPDATE, DELETE on all tables</p>
                        @elseif(in_array('Employee', $user_roles))
                            <p>🟡 <strong>employee_konibui:</strong> GRANT SELECT, INSERT, UPDATE ON limited tables</p>
                            <p class="text-xs text-blue-600">→ Cannot DELETE or modify system tables</p>
                        @else
                            <p>🟢 <strong>customer_test:</strong> GRANT SELECT ON limited tables only</p>
                            <p class="text-xs text-blue-600">→ Very restricted access for security</p>
                        @endif
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded p-4 border border-green-200">
                    <div class="flex items-start space-x-2">
                        <span class="text-lg">🎉</span>
                        <div>
                            <p class="text-sm font-medium text-green-800 mb-1">Synchronized Security Benefits:</p>
                            <ul class="text-xs text-green-700 space-y-1">
                                <li>• Same password for Laravel login AND MySQL database access</li>
                                <li>• No separate database credentials to manage</li>
                                <li>• Your role automatically determines database privileges</li>
                                <li>• Double-layer security: Application + Database level</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-100 rounded p-3">
                    <p class="text-xs text-blue-800">
                        <strong>🔍 Dev Tools Check:</strong> Open browser dev tools → Network tab → Response headers → 
                        Look for 'X-Database-Connection' to see your active MySQL connection.
                    </p>
                </div>
            </div>
        </div>

        <!-- Authorization Test Links -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">🧪 Test Other Authorization Routes</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('test.admin') }}" 
                   class="block p-4 border-2 border-red-200 rounded-lg hover:border-red-400 transition-colors">
                    <div class="text-center">
                        <div class="text-2xl mb-2">👑</div>
                        <h3 class="font-semibold text-red-700">Admin Only</h3>
                        <p class="text-sm text-gray-600">Admin role required</p>
                    </div>
                </a>

                <a href="{{ route('test.staff') }}" 
                   class="block p-4 border-2 border-blue-200 rounded-lg hover:border-blue-400 transition-colors">
                    <div class="text-center">
                        <div class="text-2xl mb-2">👨‍💼</div>
                        <h3 class="font-semibold text-blue-700">Staff Area</h3>
                        <p class="text-sm text-gray-600">Admin or Employee</p>
                    </div>
                </a>

                <a href="{{ route('test.customer') }}" 
                   class="block p-4 border-2 border-green-200 rounded-lg hover:border-green-400 transition-colors">
                    <div class="text-center">
                        <div class="text-2xl mb-2">🛍️</div>
                        <h3 class="font-semibold text-green-700">Customer Area</h3>
                        <p class="text-sm text-gray-600">Customer role required</p>
                    </div>
                </a>

                <a href="{{ route('test.authenticated') }}" 
                   class="block p-4 border-2 border-gray-200 rounded-lg hover:border-gray-400 transition-colors">
                    <div class="text-center">
                        <div class="text-2xl mb-2">🔓</div>
                        <h3 class="font-semibold text-gray-700">Any User</h3>
                        <p class="text-sm text-gray-600">Just authenticated</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Policy Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">📜 Policy Authorization Examples</h2>
            <div class="space-y-4">
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800">Product Policies</h3>
                    <div class="mt-2 space-y-1 text-sm">
                        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                            <p class="text-green-600">✅ Can create products</p>
                            <p class="text-green-600">✅ Can update products</p>
                            <p class="text-green-600">✅ Can manage inventory</p>
                        @else
                            <p class="text-red-600">❌ Cannot create products</p>
                            <p class="text-red-600">❌ Cannot update products</p>
                            <p class="text-red-600">❌ Cannot manage inventory</p>
                        @endif
                        
                        @if(auth()->user()->hasRole('Admin'))
                            <p class="text-green-600">✅ Can delete products</p>
                            <p class="text-green-600">✅ Can set pricing</p>
                        @else
                            <p class="text-red-600">❌ Cannot delete products</p>
                            <p class="text-red-600">❌ Cannot set pricing</p>
                        @endif

                        <p class="text-green-600">✅ Can view products</p>
                        <p class="text-green-600">✅ Can purchase products</p>
                    </div>
                </div>

                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800">Order Policies</h3>
                    <div class="mt-2 space-y-1 text-sm">
                        <p class="text-green-600">✅ Can create orders</p>
                        <p class="text-green-600">✅ Can view orders (filtered by role)</p>
                        
                        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Employee'))
                            <p class="text-green-600">✅ Can update order status</p>
                            <p class="text-green-600">✅ Can process orders</p>
                            <p class="text-green-600">✅ Can ship orders</p>
                        @else
                            <p class="text-red-600">❌ Cannot update order status</p>
                            <p class="text-red-600">❌ Cannot process orders</p>
                            <p class="text-red-600">❌ Cannot ship orders</p>
                        @endif

                        @if(auth()->user()->hasRole('Admin'))
                            <p class="text-green-600">✅ Can refund orders</p>
                            <p class="text-green-600">✅ Can delete orders</p>
                            <p class="text-green-600">✅ Can export orders</p>
                        @else
                            <p class="text-red-600">❌ Cannot refund orders</p>
                            <p class="text-red-600">❌ Cannot delete orders</p>
                            <p class="text-red-600">❌ Cannot export orders</p>
                        @endif

                        <p class="text-green-600">✅ Can cancel own orders</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Dashboard -->
        <div class="text-center">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                ← Back to Dashboard
            </a>
        </div>
    </div>
</div>
</x-app-layout>