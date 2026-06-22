import os
import subprocess

# 1. Create Migration
subprocess.run(["php", "artisan", "make:migration", "add_snap_token_to_tb_transaksi_table", "--table=tb_transaksi"], cwd=r"d:\Laragon\laragon\www\gamevault")

# Find the newly created migration
migration_dir = r"d:\Laragon\laragon\www\gamevault\database\migrations"
files = sorted([f for f in os.listdir(migration_dir) if "add_snap_token" in f])
migration_file = os.path.join(migration_dir, files[-1])

migration_content = f"""<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{{
    public function up()
    {{
        Schema::table('tb_transaksi', function (Blueprint $table) {{
            $table->string('snap_token')->nullable()->after('status');
        }});
    }}

    public function down()
    {{
        Schema::table('tb_transaksi', function (Blueprint $table) {{
            $table->dropColumn('snap_token');
        }});
    }}
}};
"""
with open(migration_file, 'w', encoding='utf-8') as f:
    f.write(migration_content)

# Run migration
subprocess.run(["php", "artisan", "migrate"], cwd=r"d:\Laragon\laragon\www\gamevault")


# 2. Update Transaksi.php
model_path = r"d:\Laragon\laragon\www\gamevault\app\Models\Transaksi.php"
with open(model_path, 'r', encoding='utf-8') as f:
    model_php = f.read()
model_php = model_php.replace("'status'", "'status', 'snap_token'")
with open(model_path, 'w', encoding='utf-8') as f:
    f.write(model_php)


# 3. Update CheckoutController.php
ctrl_path = r"d:\Laragon\laragon\www\gamevault\app\Http\Controllers\CheckoutController.php"
with open(ctrl_path, 'r', encoding='utf-8') as f:
    ctrl_php = f.read()

# Modify process method
ctrl_php = ctrl_php.replace(
    "Transaksi::create(['user_id'=>$user->id,'total_bayar'=>$total,'status'=>'Pending']);",
    "Transaksi::create(['user_id'=>$user->id,'total_bayar'=>$total,'status'=>'Pending']);"
)
# Wait, we need to save snap_token to DB.
# Look for $snapToken = Snap::getSnapToken($payload);
ctrl_php = ctrl_php.replace(
    "$snapToken = Snap::getSnapToken($payload);\n            return response()->json(['status'=>'success', 'snap_token' => $snapToken, 'order_id' => $trx->id]);",
    "$snapToken = Snap::getSnapToken($payload);\n            $trx->snap_token = $snapToken;\n            $trx->save();\n            return response()->json(['status'=>'success', 'snap_token' => $snapToken, 'order_id' => $trx->id]);"
)
with open(ctrl_path, 'w', encoding='utf-8') as f:
    f.write(ctrl_php)

print("DB and controllers updated")
