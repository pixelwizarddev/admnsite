import sys
import json
from telethon.sync import TelegramClient
from telethon.tl.types import ChannelParticipantsAdmins, ChannelParticipantCreator
from telethon.errors import ChannelInvalidError

api_id = '16746680'
api_hash = 'd038e172eb99839b69c39c3c25cd98cf'
phone = '84569896670'


# Connect to Telegram client
client = TelegramClient('session_name', api_id, api_hash)
client.start(phone)

def scrape_admins(channel_usernames):
    result = ""

    for channel_username in channel_usernames:
        try:
            channel = client.get_entity(channel_username)
            admins = client.get_participants(channel, filter=ChannelParticipantsAdmins)

            admin_usernames = []
            for admin in admins:
                if admin.username:
                    username = admin.username
                    status = "Admin"
                    if isinstance(admin.participant, ChannelParticipantCreator):
                        status = "Creator"
                    group_link = f"https://t.me/{channel_username}"
                    admin_usernames.append(f"{username},{status},{group_link}")

            if admin_usernames:
                result += "\n".join(admin_usernames)
            else:
                result += f"No admin usernames found for {channel_username}"
        except ChannelInvalidError:
            result += f"Cannot fetch members for {channel_username}. Invalid channel username."
        except Exception as e:
            result += f"Error occurred while fetching members for {channel_username}: {str(e)}"

        result += "\n"

    return result.strip()

# Get the channel usernames from command-line arguments
channel_usernames = sys.argv[1:]

# Scrape admins for the given channel usernames
admins = scrape_admins(channel_usernames)

# Print the admin usernames
print(admins)