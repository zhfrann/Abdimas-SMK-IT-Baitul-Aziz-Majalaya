@extends('layouts.master')

@section('title', 'Basic initialization')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Table" active="Basic initialization"/>

        <!-- [ Main Content ] start -->
        <div class="row">
          <!-- prettier-ignore -->
          <x-link title="Simple-datatables" text="A lightweight, extendable, JavaScript HTML table library written in TypeScript and transpilled to Vanilla JavaScript. Similar to jQuery DataTables for use in modern browsers, but without the jQuery dependency." link="https://github.com/fiduswriter/simple-datatables"/>
        </div>
        <div class="row">
          <!-- [ basic-table ] start -->
          <div class="col-xl-12">
            <div class="card">
              <div class="card-header">
                <h5>Basic Table</h5>
                <span class="d-block m-t-5">use class <code>table</code> inside table element</span>
              </div>
              <div class="card-body table-border-style">
                <div class="table-responsive">
                  <table class="table" id="pc-dt-simple">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Ext.</th>
                        <th>City</th>
                        <th data-type="date" data-format="YYYY/DD/MM">Start Date</th>
                        <th>Completion</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Unity Pugh</td>
                        <td>9958</td>
                        <td>Curicó</td>
                        <td>2005/02/11</td>
                        <td>37%</td>
                        <td><button type="button" class="btn btn-light-primary">Primary</button>
                      <button type="button" class="btn btn-light-primary">Primary</button>
                    <button type="button" class="btn btn-light-primary">Primary</button></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- [ basic-table ] end -->
        </div>
        <!-- [ Main Content ] end -->
@endsection

@section('scripts')
        <!-- [Page Specific JS] start -->
    <script type="module">
      import { DataTable } from '/build/js/plugins/module.js';
      window.dt = new DataTable('#pc-dt-simple');
    </script>
    <!-- [Page Specific JS] end -->
@endsection
