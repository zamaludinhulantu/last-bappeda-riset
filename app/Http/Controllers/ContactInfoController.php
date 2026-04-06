<?php

namespace App\Http\Controllers;

use App\Models\ContactInfo;
use Illuminate\Http\Request;

class ContactInfoController extends Controller
{
    public function edit()
    {
        $contact = ContactInfo::first() ?? new ContactInfo(ContactInfo::defaults());

        return view('admin.contact-info.edit', compact('contact'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'surel' => 'nullable|string|email|max:255',
            'telepon' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:500',
        ]);

        $contact = ContactInfo::firstOrNew([]);
        $contact->fill($data);
        $contact->save();

        return redirect()->route('contact-info.edit')->with('success', 'Informasi kontak berhasil disimpan.');
    }
}
