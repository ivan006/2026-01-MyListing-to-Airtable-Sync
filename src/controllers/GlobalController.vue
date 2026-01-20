<template>

  <q-layout view="lHh Lpr lFf">

    <q-header elevated>
      <q-toolbar>
        <q-toolbar-title>
          Revision Gateway
        </q-toolbar-title>
        <q-btn flat @click="drawerLeft = !drawerLeft" round dense icon="menu" />
      </q-toolbar>
    </q-header>

    <q-drawer show-if-above bordered 
        v-model="drawerLeft">
      <q-list padding>

        <q-item clickable to="/">
          <q-item-section>
            Home
          </q-item-section>
        </q-item>


      </q-list>
    </q-drawer>

    <q-page-container>

      <div class="container-lg">
        <router-view />
      </div>
    </q-page-container>

  </q-layout>
</template>

<script>
export default {
  name: 'GlobalController',
  data(){
    return {
      drawerLeft: false
    }
  }
}
</script>
