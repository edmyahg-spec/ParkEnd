<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Atraccion;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $visitas = $user->visitas()
            ->with('atraccion')
            ->orderBy('fecha', 'desc')
            ->get();

                $visitaFavorita = $user->visitas()
            ->select('atraccion_id', DB::raw('count(*) as total'), DB::raw('MAX(fecha) as ultima_visita'))
            ->groupBy('atraccion_id')
            ->orderBy('total', 'desc')
            ->orderBy('ultima_visita', 'desc')
            ->first();

        $atraccionFavorita = null;

        if ($visitaFavorita) {
            $atraccionFavorita = Atraccion::find($visitaFavorita->atraccion_id);
        }

        $totalVisitas = $user->visitas()->count();

        $clasificacion = 'Cliente Nuevo';
        $descuentoInfo = '0%';
        $progress = 0;

        if ($totalVisitas >= 20) {
            $clasificacion = 'Cliente Premium';
            $descuentoInfo = '20%';
            $progress = 100;
        } elseif ($totalVisitas >= 10) {
            $clasificacion = 'Cliente VIP';
            $descuentoInfo = '10%';
            $progress = 75;
        } elseif ($totalVisitas >= 5) {
            $clasificacion = 'Cliente Frecuente';
            $descuentoInfo = '5%';
            $progress = 40;
        } else {
            $progress = ($totalVisitas / 5) * 40;
        }

        return view('perfil.index', compact(
            'user',
            'visitas',
            'atraccionFavorita',
            'totalVisitas',
            'clasificacion',
            'descuentoInfo',
            'progress'
        ));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        $user = auth()->user();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'La contraseña actual no es correcta.',
            ]);
        }

        $user->password = $request->new_password;
        $user->save();

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}