<script setup>
import { ref, computed } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogClose,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Share2, Copy, Mail, ChevronRight } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import Facebook from '../icons/Facebook.vue';
import WhatsApp from '../icons/WhatsApp.vue';
import Card from '../ui/card/Card.vue';
import CardContent from '../ui/card/CardContent.vue';
import RestaurantController from '@/actions/App/Http/Controllers/Restaurant/RestaurantController.js';

const props = defineProps({
    restaurant: Object,
});

const open = ref(false);
// const restaurantUrl = RestaurantController.show([props.restaurant.city.slug, props.restaurant.slug]).url;
const restaurantUrl = window.location.href;
const shareUrl = computed(() => restaurantUrl);
const shareText = computed(() => {
    return (
        props.restaurant.description || `Check out ${props.restaurant.name} on Discover!`
    );
});

const shareTitle = computed(() => props.restaurant.name);

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(shareUrl.value);
        toast('The link has been copied to your clipboard.');
    } catch (err) {
        console.error(err);
        toast.error('Failed to copy');
    }
};

// Share functions
const shareOnFacebook = () => {
    const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl.value)}`;
    window.open(url, '_blank', 'width=600,height=400');
};

const shareOnWhatsApp = () => {
    const url = `https://wa.me/?text=${encodeURIComponent(shareText.value + ' ' + shareUrl.value)}`;
    window.open(url, '_blank');
};

const shareViaEmail = () => {
    const subject = `Check out ${shareTitle.value}`;
    const body = `${shareText.value}\n\nView it here: ${shareUrl.value}`;
    window.location.href = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
};
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Card>
                <CardContent class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <Share2 :size="15" />
                        <div>
                            <p class="text-sm font-medium">
                                Share {{ props.restaurant?.name }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                Help them climb today's leaderboard
                            </p>
                        </div>
                    </div>
                    <ChevronRight />
                </CardContent>
            </Card>
        </DialogTrigger>

        <DialogContent class="sm:max-w-md bg-card text-card-foreground">
            <DialogHeader>
                <DialogTitle>Share</DialogTitle>
                <DialogDescription>
                    Share <strong>{{ props.restaurant?.name }}</strong> with your friends on social media
                </DialogDescription>
            </DialogHeader>

            <div class="flex items-center space-x-2">
                <div class="grid flex-1 gap-2">
                    <Label for="link" class="sr-only">Link</Label>
                    <Input id="link" :default-value="restaurantUrl" readonly @click="copyToClipboard" />
                </div>
                <Button type="submit" size="sm" class="px-3" @click="copyToClipboard">
                    <span class="sr-only">Copy</span>
                    <Copy class="h-4 w-4" />
                </Button>
            </div>

            <div class="flex items-center justify-between gap-2 bg-card text-card-foreground">
                <!-- Facebook -->
                <Button variant="outline" class="gap-2" @click="shareOnFacebook">
                    <Facebook />
                    Facebook
                </Button>

                <!-- WhatsApp -->
                <Button variant="outline" class="gap-2" @click="shareOnWhatsApp">
                    <WhatsApp />
                    WhatsApp
                </Button>

                <!-- Email -->
                <Button variant="outline" class="gap-2" @click="shareViaEmail">
                    <Mail class="h-5 w-5" />
                    Email
                </Button>
            </div>

            <!-- <DialogFooter class="sm:justify-start">
                <DialogClose as-child>
                    <Button type="button" variant="secondary">Close</Button>
                </DialogClose>
            </DialogFooter> -->
        </DialogContent>
    </Dialog>
</template>