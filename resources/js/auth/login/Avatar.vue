<script setup
        lang="ts">
        import { Avatar, AvatarFallback, AvatarImage } from '@shadcn/components/ui/avatar'
        import { HTMLAttributes, ref } from 'vue';
        import { cn } from '@shadcn/lib/utils'
        import DropdownMenuTrigger from '@shadcn/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
        import DropdownMenu from '@shadcn/components/ui/dropdown-menu/DropdownMenu.vue';
        import DropdownMenuContent from '@shadcn/components/ui/dropdown-menu/DropdownMenuContent.vue';
        import DropdownMenuLabel from '@shadcn/components/ui/dropdown-menu/DropdownMenuLabel.vue';
        import DropdownMenuSeparator from '@shadcn/components/ui/dropdown-menu/DropdownMenuSeparator.vue';
        import DropdownMenuGroup from '@shadcn/components/ui/dropdown-menu/DropdownMenuGroup.vue';
        import DropdownMenuItem from '@shadcn/components/ui/dropdown-menu/DropdownMenuItem.vue';
        import { LogIn, LogOut, User } from 'lucide-vue-next';
        import { Button } from '@shadcn/components/ui/button';
        // import { isAuthTokenExpire } from '@@/js/utils/autentication';
        import axios from 'axios';
        // import { logout } from '@@/js/utils/autentication';
        import { useRoute } from 'vue-router';
        import Spinner from '@shadcn/components/ui/spinner/Spinner.vue';
        import { Primitive } from 'reka-ui';
        import { isAuth } from 'resources/js/utils/autentication';
        import { api } from 'resources/js/utils/fetch';
        import { LOGOUT_URI } from 'resources/js/config/app.config';

        const props = withDefaults(
          defineProps<{
            as?: string
            class?: HTMLAttributes["class"]
            asChild?: boolean
          }>(),
          {
            as: "div", // default jadi <div>
          }
        )

        // let isAuth = !isAuthTokenExpire();
        const route = useRoute();

        const logoutSpin = ref(false);
        function logoutAccount() {
          logoutSpin.value = true;
          api.get(LOGOUT_URI)
            .then(() => {
              logoutSpin.value = false
              const currentUrl = route.fullPath;
              window.location.href = "/login?redirect_to=" + currentUrl;
            })
        }

</script>

<template>
  <Primitive data-slot="div" :as="as" :as-child="asChild" :class="cn('', props.class)">
    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <Avatar as="div">
          <AvatarImage src="https://github.com/unovue.png" alt="@unovue" />
          <AvatarFallback>CN</AvatarFallback>
        </Avatar>
      </DropdownMenuTrigger>
      <DropdownMenuContent class="w-56">
        <DropdownMenuLabel>Authentication</DropdownMenuLabel>
        <DropdownMenuSeparator />
        <DropdownMenuGroup v-if="isAuth">
          <DropdownMenuItem>
            <User class="mr-2 h-4 w-4" />
            <span>Profile</span>
          </DropdownMenuItem>
          <DropdownMenuItem @click="logoutAccount">
            <LogOut class="mr-2 h-4 w-4" />
            <button>Logout</button>
            <Spinner v-if="logoutSpin" />
          </DropdownMenuItem>
        </DropdownMenuGroup>
        <DropdownMenuGroup v-else>
          <DropdownMenuItem>
            <LogIn class="mr-2 h-4 w-4" />
            <button>Login</button>
          </DropdownMenuItem>
        </DropdownMenuGroup>
      </DropdownMenuContent>
    </DropdownMenu>
  </Primitive>
  <!-- <div :class="cn('w-full', props.class)"></div> -->
</template>