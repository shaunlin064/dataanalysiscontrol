<template>
  <div>
    <div style='float:left'>
      <div class='btn-group dropright'>
        <button type='button' class='btn btn-dropbox dropdown-toggle' data-toggle='dropdown'
                aria-haspopup='true' aria-expanded='false'>選擇
          <span class='caret'></span>
        </button>
        <ul class='dropdown-menu' role='menu'>
          <li><a href='#' @click="quickClick('all')">All</a></li>
          <li><a href='#' @click='quickClick(1)'>媒體</a></li>
          <li><a href='#' @click='quickClick(2)'>口碑</a></li>
          <li><a href='#' @click='quickClick(3)'>社群</a></li>
          <li><a href='#' @click="quickClick('clear')">Clear</a></li>
        </ul>
      </div>
    </div>
    <div class='col-xs-11'>
      <select class='select2' :id='id' :multiple='multiple' :data-placeholder='placeholder'
              style='width: 100%;'>
        <optgroup label='媒體Pm'>
          <option v-if='item.type === 1' :value='item.user.erp_user_id' v-for='item in row' :selected='true'>
            媒體-{{ item.user.name }}
          </option>
        </optgroup>
        <optgroup label='口碑Pm'>
          <option v-if='item.type === 2' :value='item.user.erp_user_id' v-for='item in row' :selected='true'>
            口碑-{{ item.user.name }}
          </option>
        </optgroup>
        <optgroup label='社群Pm'>
          <option v-if='item.type === 3' :value='item.user.erp_user_id' v-for='item in row' :selected='true'>
            社群-{{ item.user.name }}
          </option>
        </optgroup>
      </select>
    </div>
  </div>
</template>
<script>
import {mapActions, mapState} from 'vuex';
import * as types from "../store/types";

export default {
  name: "Select2Pm",
  props: {
    id: String,
    multiple: Boolean,
    disabled: Boolean,
    selected: Boolean,
    placeholder: String,
    row: Array
  },
  data() {
    return {
      pmType: {
        "1":[],
        "2":[],
        "3":[]
      },
      selectDom : '',
    }
  },
  computed: {
    ...mapState('select', ['select_pm']),
  },
  beforeMount: function () {

  },
  mounted: function () {

    var thisVue = this;
    var domId = thisVue.id;

    this.row.map(function ($v) {
      thisVue.pmType[$v.type].push($v.user.erp_user_id);
    });
    this.selectDom = $('#' + domId + '').select2();
    thisVue.updateSelectToVux();

    $('#' + domId + '').on('change', function (e) {
      thisVue.updateSelectToVux();
    });

  },
  methods: {
    ...mapActions('select', [types.CHANGE_SELECT_PM]),
    updateSelectToVux() {
        this[types.CHANGE_SELECT_PM]($('#' + this.id + '').val());
    },
    quickClick(type) {
      let selectTarget = [];
      if(isNaN(type)){
        if( type === 'all'){
          selectTarget = this.row.map(function(v){
            return v.user.erp_user_id;
          });
        }else{
          selectTarget = null;
        }
      }else {
        selectTarget = this.pmType[type];
      }
      this.selectDom.val(selectTarget).trigger("change");
    }
  },
  updated() {
    // console.log('view updated')
  },
  watch: {
    // change_date: {
    //     immediate: true,
    //     handler(val, oldVal) {
    //         if (oldVal !== undefined) {
    //             this.getCampaignData(this.user_ids, val);
    //         }
    //     }
    // }
  }
}
</script>

<style scoped>

</style>