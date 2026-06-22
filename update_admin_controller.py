with open('app/Http/Controllers/AdminController.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

new_method = """
    /**
     * Manajemen Pengguna
     */
    public function indexUsers(Request $request) {
        $query = User::where('role', 'user')->with([
            'wishlists.game', 
            'keranjangs.game', 
            'transaksis' => function($q) {
                $q->where('status', 'Success')->with('details.game');
            }
        ])->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate(10);
        return view('admin.users_index', compact('users'));
    }
}
"""

# Find the last closing brace of the class
for i in range(len(lines)-1, -1, -1):
    if lines[i].strip() == '}':
        lines[i] = new_method
        break

with open('app/Http/Controllers/AdminController.php', 'w', encoding='utf-8') as f:
    f.writelines(lines)
print('Updated AdminController.php')
