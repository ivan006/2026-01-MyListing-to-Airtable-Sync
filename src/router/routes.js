const routes = [
  {
    path: '/',
    component: () => import('src/controllers/GlobalController.vue'),
    meta: { requiresAuth: false },
    children: [
      {
        path: '/',
        component: () => import('src/controllers/HomeComponent.vue'),
        meta: { requiresAuth: false }
      }
    ]
  },

  {
    path: '/:catchAll(.*)*',
    component: () => import('src/controllers/ErrorNotFound.vue'),
    meta: { requiresAuth: false }
  }
];

export default routes;
