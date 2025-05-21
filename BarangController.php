<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 
use App\Models\Barang;

class BarangController extends Controller
{
    public function index() 
    {
        $barangs = Barang::all();
        return view('barangs.index', compact('barangs'));
    }

    public function create() {
        return view('barangs.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'kode' => 'required|unique:barangs',
            'nama_barang' => 'required',
            'deskripsi' => 'nullable',
            'harga_satuan' => 'required|numeric',
            'jumlah' => 'required|integer',
            'foto' => 'image|nullable'
        ]);

        if ($request->file('foto')) {
            $validated['foto'] = $request->file('foto')->store('foto-barang', 'public');
        }        

        Barang::create($validated);
        return redirect()->route('barangs.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(Barang $barang) {
        return view('barangs.edit', compact('barang'));
    }

    public function update(Request $request, Barang $barang) {
        $validated = $request->validate([
            'kode' => 'required|unique:barangs,kode,' . $barang->id,
            'nama_barang' => 'required',
            'deskripsi' => 'nullable',
            'harga_satuan' => 'required|numeric',
            'jumlah' => 'required|integer',
            'foto' => 'image|nullable'
        ]);

        if ($request->hasFile('foto')) {
            if ($barang->foto) {
                Storage::delete($barang->foto);
            }
            $validated['foto'] = $request->file('foto')->store('foto-barang');
        }

        $barang->update($validated);
        return redirect()->route('barangs.index')->with('success', 'Data berhasil diupdate.');
    }

    public function destroy(Barang $barang) {
        if ($barang->foto) {
            Storage::delete($barang->foto);
        }
        $barang->delete();
        return redirect()->route('barangs.index')->with('success', 'Data berhasil dihapus.');
    }
}